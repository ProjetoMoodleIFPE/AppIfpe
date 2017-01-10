 <?php
 session_start();
  $Disciplina = $_POST['disciplina'];
include 'php/Conexao.php'; 
$stmt = $conexao->prepare("SELECT aluno.token FROM aluno,aluno_disciplina WHERE aluno.idaluno = aluno_disciplina.aluno_idaluno AND aluno_disciplina.disciplina_iddisciplina =? AND aluno.alunoativo != 0;");
$DevicesX = null;

$stmt->bindValue(1,$Disciplina);
		


		$stmt->execute();

		if($stmt->rowCount() >0){

	
		

	$resultado = $stmt->fetchAll();

	foreach($resultado as $linha){

$DevicesX = $linha["token"] .",". $DevicesX  ;

				

	
	

	
		}
		
		}

class GCMPushMessage {
    var $url = 'https://android.googleapis.com/gcm/send';
    var $serverApiKey = "";
    var $devices = array();
    
    function GCMPushMessage($apiKeyIn){
        $this->serverApiKey = $apiKeyIn;
    }
    function setDevices($deviceIds){
    
        if(is_array($deviceIds)){
            $this->devices = $deviceIds;
        } else {
            $this->devices = array($deviceIds);
        }
    
    }
    function send($message, $data = false){
        
        if(!is_array($this->devices) || count($this->devices) == 0){
            $this->error("No devices set");
        }
        
        if(strlen($this->serverApiKey) < 8){
            $this->error("Server API Key not set");
        }
        
        $fields = array(
            'registration_ids'  => $this->devices,
            'data'              => array( "message" => $message ),
        );
        
        if(is_array($data)){
            foreach ($data as $key => $value) {
                $fields['data'][$key] = $value;
            }
        }
        $headers = array( 
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );

        echo json_encode( $fields );
        $ch = curl_init();
        
        curl_setopt( $ch, CURLOPT_URL, $this->url );
        
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
        
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($ch);
        
        curl_close($ch);
        
        return $result;
    }
    
    function error($msg){
        echo "Android send notification failed with error:";
        echo "\t" . $msg;
        exit(1);
    }
}

if(isset($_POST) && !empty($_POST)){
	$apiKey = $_POST["apiKey"]; // CHAVE DA API GCM...
    $devicesToken = explode(",",$DevicesX); // Tokens dos aparelhos que receberão a notificação - separados por ','...
	$gcpm = new GCMPushMessage($apiKey);
	$gcpm->setDevices($devicesToken);
    $imagemUrl = $_POST["imagemurl"];
    $urlPush = $_POST["url"];


	$title = $_POST["title"];
	$message = $_POST["message"]; // Conteúdo da mensagem do PUSH...
	$response = $gcpm->send($message, array('title' => $title,'BigPictureStyle'=>'true','BigTextStyle' => 'true','bigText' => $message ,
                                                'imgUrl'=>$imagemUrl,'url'=>$urlPush,'customNotificationTitle'=>$title,
                                                'button1Icon' => 'ic_share', 'button1Title' => 'Acessar Conteudo' , 'button1Action' => $urlPush
   )); // Título do Push...
   
   // SALVAR NOTIFICAÇÃO...
    $IdUsuario = $_SESSION['idusuario'];
	$TipoNotificacao = $_POST['tiponotificacao'];
include 'php/Conexao.php';
 $stmt = $conexao->prepare("insert into notificacao(usuario_idusuario,tiponotificacao_idtiponotificacao,descricao,urlimagem,linknotificacao,titulonotificacao) values (?,?,?,?,?,?)");
	
	

	$stmt -> bindParam(1,$IdUsuario);
	$stmt -> bindParam(2,$TipoNotificacao);
	$stmt -> bindParam(3,$message);
	$stmt -> bindParam(4,$imagemUrl); 
	$stmt -> bindParam(5,$urlPush);	
	$stmt -> bindParam(6,$title);
	


$stmt->execute(); 
   
	echo $response;
	echo "<br>-------------------------------------";
	echo $_SESSION['idusuario'];
}