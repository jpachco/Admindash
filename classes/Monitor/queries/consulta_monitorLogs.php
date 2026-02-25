<?php
require_once __DIR__ . '/../../../config/config.php';
//error_reporting(0);

global $conexionMSDB;
$ds=Database::getConnection();
$rows_lab=array();
$i=0;
$resultadoMSDB=array();

$cons_mon_lab=utf8_encode("
WITH running as (
SELECT
    ja.job_id,
    j.name AS job_name,
    ja.start_execution_date,      
    'Ejecutando'as Stat,--ISNULL(last_executed_step_id,0)+1 AS current_executed_step_id,
    Js.step_name
FROM msdb.dbo.sysjobactivity ja 
LEFT JOIN msdb.dbo.sysjobhistory jh ON ja.job_history_id = jh.instance_id
JOIN msdb.dbo.sysjobs j ON ja.job_id = j.job_id
JOIN msdb.dbo.sysjobsteps js
    ON ja.job_id = js.job_id
    AND ISNULL(ja.last_executed_step_id,0)+1 = js.step_id
WHERE
  ja.session_id = (
    SELECT TOP 1 session_id FROM msdb.dbo.syssessions ORDER BY agent_start_date DESC
  )
AND start_execution_date is not null
AND stop_execution_date is null

)

select 
		[Nombre],
		[UltimaFechaEjecucion],
		case when  running.job_id IS null  then  [EstadoUltimaEjecucion]  else running.Stat end [EstadoUltimaEjecucion] ,
		  message,
		[DuracionUltimaEjecucion(HH:MM:SS)],
		[FechaProximaEjecucion]

 from 

(

SELECT
   sj.job_id,
     [sj].[name] AS [Nombre]
    , CASE
        WHEN [jh].[run_date] IS NULL OR [jh].[run_time] IS NULL THEN NULL
        ELSE CAST(
                CAST([jh].[run_date] AS CHAR(8))
                + ' '
                + STUFF(
                    STUFF(RIGHT('000000' + CAST([jh].[run_time] AS VARCHAR(6)),  6), 3, 0, ':'), 6, 0, ':')
                AS DATETIME)
      END AS [UltimaFechaEjecucion]
    , CASE [jh].[run_status]
        WHEN 0 THEN 'Fallido'
        WHEN 1 THEN 'Completado'
        WHEN 2 THEN 'Reintento'
        WHEN 3 THEN 'Cancelado'
        WHEN 4 THEN 'Ejecutando' -- In Progress
      END AS [EstadoUltimaEjecucion],
	  jh.message
    , STUFF(STUFF(RIGHT('000000' + CAST([jh].[run_duration] AS VARCHAR(6)),  6), 3, 0, ':'), 6, 0, ':') AS [DuracionUltimaEjecucion(HH:MM:SS)]
    , CASE [sJOBSCH].[NextRunDate]
        WHEN 0 THEN NULL
        ELSE CAST(
                CAST([sJOBSCH].[NextRunDate] AS CHAR(8))
                + ' '
                + STUFF(STUFF(RIGHT('000000' + CAST([sJOBSCH].[NextRunTime] AS VARCHAR(6)),  6), 3, 0, ':'), 6, 0, ':') AS DATETIME)
      END AS [FechaProximaEjecucion]
FROM
    [msdb].[dbo].[sysjobs] AS [sj]
    LEFT JOIN (
                SELECT
                    [job_id]
                    , MIN([next_run_date]) AS [NextRunDate]
                    , MIN([next_run_time]) AS [NextRunTime]
                FROM [msdb].[dbo].[sysjobschedules]
                GROUP BY [job_id]
            ) AS [sJOBSCH]
        ON [sj].[job_id] = [sJOBSCH].[job_id]
    LEFT JOIN (
                SELECT
                    [sjh].[job_id]
                    , [sjh].[run_date]
                    , [sjh].[run_time]
                    , [sjh].[run_status]
                    , [sjh].[run_duration]
                    , [sjh].[message]
					, [sjh].[sql_message_id]
                    , ROW_NUMBER() OVER (
                                            PARTITION BY sjh.[job_id] 
                                            ORDER BY [sjh].[run_date] DESC, [sjh].[run_time] DESC
                      ) AS RowNumber
                    , [sja].[last_executed_step_id]
                    , [sjs].[step_name] [last_executed_step]
                    , [sja].[last_executed_step_date]
                FROM [msdb].[dbo].[sysjobhistory] sjh
                    LEFT OUTER JOIN [msdb].[dbo].[sysjobactivity] sja ON sja.job_id = sjh.job_id
                    LEFT OUTER JOIN [msdb].[dbo].[sysjobsteps] sjs ON sjs.job_id = sja.job_id AND sjs.step_id = sja.last_executed_step_id
                WHERE sjh.[step_id] = 1
            ) AS [jh]
        ON [sj].[job_id] = [jh].[job_id]
        AND [jh].[RowNumber] = 1
        
	where sj.name not in('syspolicy_purge_history','Trunca Logs de  Bases de Datos'))lvl full outer join running 
	on lvl.job_id=running.job_id


ORDER BY [Nombre]

  
  
  ");


$stmt =$ds->query($cons_mon_lab);
$rows_lab =$stmt->fetchAll(PDO::FETCH_ASSOC);


$columnas_lab=count($rows_lab[0]);
$filas_lab=count($rows_lab);

$data_lab=array();
for($i=0;$i<$filas_lab;$i++){



 $data_lab[$i][0]=$rows_lab[$i]['Nombre'];
 $data_lab[$i][1]=date_format(date_create($rows_lab[$i]['UltimaFechaEjecucion']), 'Y/m/d H:i:s');
 $data_lab[$i][2]=$rows_lab[$i]['EstadoUltimaEjecucion'];
 $data_lab[$i][3]=$rows_lab[$i]['message'];
 $data_lab[$i][4]=$rows_lab[$i]['DuracionUltimaEjecucion(HH:MM:SS)'];
 $data_lab[$i][5]=date_format(date_create($rows_lab[$i]['FechaProximaEjecucion']), 'Y/m/d H:i:s');




}

$filas_data=count($data_lab);
$columnas_data=count($data_lab[0]);
$estructura_log="";
$estructura_log.='<table class="table table-custom table-striped" id="jobs">';

$estructura_log.='  <thead><tr> 
<th>ACTIVIDAD</th>
<th>ULTIMA EJECUCION</th>
<th>ESTADO</th>
<th>DURACION</th>
</tr></thead><tbody>';


for ($i=0;$i<$filas_data;$i++){
 $estructura_log.='<tr>';
 $estructura_log.= "<td>".$data_lab[$i][0]."</td>\n";
  $estructura_log.='<td> '. $data_lab[$i][1]."</td> \n";
  if($data_lab[$i][2]=='Fallido' ){
   $background=" color:red;  ";
  }
 elseif($data_lab[$i][2]=='Cancelado' ){
  $background=" color:orange;  ";
 }
  elseif($data_lab[$i][2]=='Ejecutando' ){
   $background=" color:blue;  ";
  }
  else{$background=" color:green; ";}

  $estructura_log.='<td  data-original-title="Toggle Navigation"  style="'.$background.'">'. $data_lab[$i][2]."</td> \n";
 $estructura_log.='<td>'.$data_lab[$i][3]."</td> \n"; 
  $estructura_log.='<td>'.$data_lab[$i][4]."</td> \n";
 $estructura_log.='</tr>';

}


array_push($resultadoMSDB,$estructura_log.'<tbody></table>');


 ?>
