<?php
/**
 *
 */
class DbOperations
{
  private $con;

  function __construct()
  {
    require_once (dirname(__FILE__).'/DbConnect.php');
    $db=new DbConnect();
    $this->con=$db->connect();
  }

  public function createUser($username,$pass,$mojodi,$edit){
    if($this->isUserExist($username)&&$edit==0){
      return 0;
    }else{
        $password=md5($pass);
        if($edit=="0"){
        $stmt=$this->con->prepare("INSERT INTO users (username,password,mojodi) VALUES (?,?,?)");
        $stmt->bindValue("1",$username);
        $stmt->bindValue("2",$password);
        $stmt->bindValue("3",$mojodi);
      }else{
        $stmt=$this->con->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bindValue("1",$edit);
        $stmt->execute();
      //  $result=$stmt->get_result();
      //  $row=$result->fetch_assoc();
       $row=$stmt->fetch(PDO::FETCH_ASSOC);

        $c=strcmp($row['username'],$username);
        if($c!=0&&$this->isUserExist($username))return 0;
        $stmt=$this->con->prepare("UPDATE users SET username=?,password=?,mojodi=? WHERE id=?");
        $stmt->bindValue("1",$username);
        $stmt->bindValue("2",$password);
        $stmt->bindValue("3",$mojodi);
        $stmt->bindValue("4",$edit);
      }
        if($stmt->execute()){
          if ($edit=="0"){
            $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi+?");
            $stmt->bindValue("1",$mojodi);

          }
          else {
            $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi-?+?");
            $stmt->bindValue("1",$row['mojodi']);
            $stmt->bindValue("2",$mojodi);
          }
          $stmt->execute();
        return 1;
    }else{
     return 2;
    }
  }
  }

  public function userLogin($username,$pass){
    $password=md5($pass);
    $stmt=$this->con->prepare("SELECT count(*) FROM users WHERE username = ? AND password = ? ");
    $stmt->bindValue("1",$username);
    $stmt->bindValue("2",$password);
    $stmt->execute();
    //$stmt->store_result();
    return $stmt->fetchColumn()>0;
  }

  public function getUserByUsername($username){
    $stmt=$this->con->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bindValue("1",$username);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);

    return $data;
  }

  public function displayUser($name){
   if($name=='all'){
         $stmt=$this->con->prepare("SELECT * FROM users ORDER BY username");
    }
    else{
          $stmt=$this->con->prepare("SELECT * FROM users WHERE username LIKE '$name%'");

   }
    $stmt->execute();
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $data;
  }

  public function displayVam($name){
    if($name=='all'){
       $stmt=$this->con->prepare("SELECT * FROM wam where tasfiya=false ORDER BY t_pardakhtshoda DESC");
     }
    else{
       $stmt=$this->con->prepare("SELECT * FROM wam where tasfiya=false AND username LIKE '%$name'");
   }
    $stmt->execute();
    $data=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $data;
  }

  public function deleteUser($id){
    $stmt=$this->con->prepare("SELECT mojodi FROM users  WHERE id=?");
    $stmt->bindValue(1,$id);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);

    $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi-?");
    $stmt->bindValue(1,$data['mojodi']);
    $stmt->execute();

    $stmt=$this->con->prepare("DELETE FROM users  WHERE id=?");
    $stmt->bindValue(1,$id);
    $stmt->execute();
  }

  public function Variz($id,$mablagh){
    $stmt=$this->con->prepare("UPDATE users SET mojodi=mojodi+? WHERE id=?");
    $stmt->bindValue(1,$mablagh);
    $stmt->bindValue(2,$id);
    if($stmt->execute()){
      $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi+?");
      $stmt->bindValue(1,$mablagh);
      if($stmt->execute())
         return 1;
      else
          Return 0;
    }




  }

  public function tasfiyaVam($id){
    $stmt=$this->con->prepare("SELECT m_vam,m_aghsat,t_pardakhtshoda,username,codes FROM wam  WHERE id=?");
    $stmt->bindValue(1,$id);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);
    $mandah=$data['m_vam']-($data['m_aghsat']*$data['t_pardakhtshoda']);

    $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi+?");
    $stmt->bindValue(1,$mandah);
    $stmt->execute();

    $stmt=$this->con->prepare("UPDATE wam SET tasfiya=true  WHERE id=?");
    $stmt->bindValue(1,$id);
    $stmt->execute();

    $arr=explode(",",$data['codes']);
    $count=count($arr);
    for($i=0;$i<$count;$i++){
      $stmt=$this->con->prepare("UPDATE users SET wam=0 WHERE id=?");
      $stmt->bindValue(1,$arr[$i]);
      $stmt->execute();
    }

    $stmt=$this->con->prepare("SELECT codes FROM wam WHERE tasfiya=false AND username=?");
    $stmt->bindValue(1,$data['username']);
    $stmt->execute();
    $result1=$stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($result1 as $row1){
    $arr=explode(",",$row1['codes']);
    $count=count($arr);
    for($i=0;$i<$count;$i++){
      $stmt=$this->con->prepare("UPDATE users SET wam=1 WHERE id=?");
      $stmt->bindValue(1,$arr[$i]);
      $stmt->execute();
    }
    }

  //  $this->updateWam();

  }

  public function deleteVam($id){
    $stmt=$this->con->prepare("SELECT m_vam,codes,username FROM wam  WHERE id=?");
    $stmt->bindValue(1,$id);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);

    $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi+?");
    $stmt->bindValue(1,$data['m_vam']);
    $stmt->execute();


    $stmt=$this->con->prepare("DELETE FROM wam  WHERE id=?");
    $stmt->bindValue(1,$id);
    $stmt->execute();


      $arr=explode(",",$data['codes']);
      $count=count($arr);
      for($i=0;$i<$count;$i++){
        $stmt=$this->con->prepare("UPDATE users SET wam=0 WHERE id=?");
        $stmt->bindValue(1,$arr[$i]);
        $stmt->execute();
      }

      $stmt=$this->con->prepare("SELECT codes FROM wam WHERE tasfiya=false AND username=?");
      $stmt->bindValue(1,$data['username']);
      $stmt->execute();
      $result1=$stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($result1 as $row1){
      $arr=explode(",",$row1['codes']);
      $count=count($arr);
      for($i=0;$i<$count;$i++){
        $stmt=$this->con->prepare("UPDATE users SET wam=1 WHERE id=?");
        $stmt->bindValue(1,$arr[$i]);
        $stmt->execute();
      }
      }


    //$this->updateWam();

  }



  public function getnewmojodi($id){
    $stmt=$this->con->prepare("SELECT * FROM mojodi WHERE id=?");
    $stmt->bindValue("1",$id);
    $stmt->execute();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
  }

  private function isUserExist($username){
    $stmt=$this->con->prepare("SELECT count(*) FROM users WHERE username = ?");
    $stmt->bindValue("1",$username);
    $stmt->execute();
    //$stmt->store_result();
    return $stmt->fetchColumn()>0;
  }
  public function createSandogh($name,$value_mahiyane,$t_aghsat,$mizan){
   if($this->isSandoghExist()){
     $stmt=$this->con->prepare("UPDATE sandogh SET name=?,value_mahiyane=?,t_aghsat=?,mizan=?");
     $stmt->bindValue("1",$name);
     $stmt->bindValue("2",$value_mahiyane);
     $stmt->bindValue("3",$t_aghsat);
     $stmt->bindValue("4",$mizan);
     if($stmt->execute()){return 1;}else{return 2;}
      }else{
        $stmt=$this->con->prepare("INSERT INTO sandogh (name,value_mahiyane,t_aghsat,mizan) VALUES (?,?,?,?)");
        $stmt->bindValue("1",$name);
        $stmt->bindValue("2",$value_mahiyane);
        $stmt->bindValue("3",$t_aghsat);
        $stmt->bindValue("4",$mizan);
        if($stmt->execute()){

        return 1;
    }else{
     return 2;
    }
  }
  }

  public function isSandoghExist(){
    $stmt=$this->con->prepare("SELECT count(*) FROM sandogh ");
    $stmt->execute();
    //$stmt->store_result();
    return $stmt->fetchColumn()>0;
  }

  public function updateMojodi(){

    $stmt=$this->con->prepare("SELECT value_mahiyane FROM sandogh");
    $stmt->execute();
    //$result=$stmt->get_result();
    //$row=$result->fetch_assoc();
    $row=$stmt->fetch(PDO::FETCH_ASSOC);


    $stmt=$this->con->prepare("DELETE FROM mojodi  WHERE mojodi.mah=0");
    $stmt->execute();

    $s=$this->tuser("users")-$this->tuser("mojodi");
    $m=$row['value_mahiyane']*$s;


    $stmt=$this->con->prepare("UPDATE users LEFT JOIN mojodi on users.id=mojodi.id SET mojodi=mojodi+? WHERE mojodi.id is null or mojodi.mah=0");
    $stmt->bindValue(1,$row['value_mahiyane']);
    $stmt->execute();

    $stmt=$this->con->prepare("UPDATE users  JOIN mojodi on users.id=mojodi.id SET mojodi=mojodi+mojodi.mablagh,mojodi.mah=mojodi.mah-1 WHERE mojodi.mah!=0");
    $stmt->execute();

    $stmt=$this->con->prepare("UPDATE wam SET t_pardakhtshoda=t_pardakhtshoda+1 WHERE t_pardakhtshoda<t_aghsat AND next_mah!=true");
    $stmt->execute();

    $stmt=$this->con->prepare("SELECT SUM(m_aghsat) AS value_sum FROM wam WHERE t_pardakhtshoda<=t_aghsat AND tasfiya!=true AND next_mah!=true");
    $stmt->execute();
    //$result=$stmt->get_result();
    //$row=$result->fetch_assoc();
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    $sum=$row['value_sum'];
    if($sum==0) $sum=0;

    $stmt=$this->con->prepare("SELECT SUM(mablagh) AS value_sum FROM mojodi");
    $stmt->execute();
    //$result=$stmt->get_result();
  //  $row=$result->fetch_assoc();
  $row=$stmt->fetch(PDO::FETCH_ASSOC);
    $sum1=$row['value_sum'];
    if($sum1==0) $sum1=0;

    //*****************************************************//
    $stmt=$this->con->prepare("SELECT username,codes FROM wam WHERE tasfiya=false AND t_pardakhtshoda=t_aghsat");
    $stmt->execute();
    $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
      $arr=explode(",",$row['codes']);
      $count=count($arr);
      for($i=0;$i<$count;$i++){
        $stmt=$this->con->prepare("UPDATE users SET wam=0 WHERE id=?");
        $stmt->bindValue(1,$arr[$i]);
        $stmt->execute();
      }
    }
    //*****************************************************//
    $stmt=$this->con->prepare("UPDATE wam SET tasfiya=true WHERE t_pardakhtshoda=t_aghsat");
    $stmt->execute();

    //******************************************************//
    foreach($result as $row){
      $stmt=$this->con->prepare("SELECT codes FROM wam WHERE tasfiya=false AND username=?");
      $stmt->bindValue(1,$row['username']);
      $stmt->execute();
      $result1=$stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach($result1 as $row1){
      $arr=explode(",",$row1['codes']);
      $count=count($arr);
      for($i=0;$i<$count;$i++){
        $stmt=$this->con->prepare("UPDATE users SET wam=1 WHERE id=?");
        $stmt->bindValue(1,$arr[$i]);
        $stmt->execute();
      }
      }
    }
    //*********************************************************//

    $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi+?+?+?");
    //$stmt->bind_param("sss",$m,$sum,$sum1);
    $stmt->bindValue(1,$m);
    $stmt->bindValue(2,$sum);
    $stmt->bindValue(3,$sum1);
    $stmt->execute();

    $stmt=$this->con->prepare("UPDATE wam SET next_mah=false WHERE next_mah=true");
    $stmt->execute();

    //$this->updateWam();

}

 public function updateWam(){
   $stmt=$this->con->prepare("UPDATE users SET wam=0");
   $stmt->execute();

   $stmt=$this->con->prepare("SELECT codes FROM wam WHERE tasfiya=false");
   $stmt->execute();
   $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

   foreach($result as $row){
     $arr=explode(",",$row['codes']);
     $count=count($arr);
     for($i=0;$i<$count;$i++){
       $stmt=$this->con->prepare("UPDATE users SET wam=1 WHERE id=?");
       $stmt->bindValue(1,$arr[$i]);
       $stmt->execute();
     }
   }

 }
  public function newVam($username,$m_vam,$t_aghsat,$m_aghsat,$t_pardakhtshoda,$tozihat,$codes,$tf){
    if($tf=="true") $tf="1";else $tf="0";
    $codesarray=json_decode($codes);
    $string=implode(",",$codesarray);
    $stmt=$this->con->prepare("INSERT INTO wam (username,m_vam,t_aghsat,m_aghsat,t_pardakhtshoda,tozihat,codes,tarikh,next_mah) VALUES (?,?,?,?,?,?,?,now(),?)");
    $stmt->bindValue("1",$username);
    $stmt->bindValue("2",$m_vam);
    $stmt->bindValue("3",$t_aghsat);
    $stmt->bindValue("4",$m_aghsat);
    $stmt->bindValue("5",$t_pardakhtshoda);
    $stmt->bindValue("6",$tozihat);
    $stmt->bindValue("7",$string);
    $stmt->bindValue("8",$tf);
    if($stmt->execute()){
        $m_pardakhshoda=$m_aghsat*$t_pardakhtshoda;
        $m_manda=$m_vam-$m_pardakhshoda;
      $stmt=$this->con->prepare("UPDATE sandogh SET smojodi=smojodi-?");
      $stmt->bindValue("1",$m_manda);
      $stmt->execute();

      $count=count($codesarray);
      for($i=0;$i<$count;$i++){
        $stmt=$this->con->prepare("UPDATE users SET wam=1 WHERE id=?");
        $stmt->bindValue("1",$codesarray[$i]);
        $stmt->execute();
    }
      return 1;
    }
    else{

      return 0;
    }
  }

  public function AfzayesheMojodi($id,$mablagh,$mah){
    $row=$this->getnewmojodi($id);
    if ($row['id']==null){
    $stmt=$this->con->prepare("INSERT INTO mojodi (id,mablagh,mah,tarikh) VALUES (?,?,?,now())");
    $stmt->bindValue("1",$id);
    $stmt->bindValue("2",$mablagh);
    $stmt->bindValue("3",$mah);
    if($stmt->execute()){
      return 1;
    }
    else{
      return 0;
    }

}else{
  $stmt=$this->con->prepare("UPDATE mojodi SET mablagh=?,mah=? WHERE id=?");
  $stmt->bindValue("1",$mablagh);
  $stmt->bindValue("2",$mah);
  $stmt->bindValue("3",$id);
  if($stmt->execute()){
    return 1;
  }
  else{
    return 0;
  }

}
  }

  public function tuser($name){
    if($name=="wam") {
      $stmt=$this->con->prepare("SELECT count(*) FROM $name WHERE tasfiya!=true ");
    }else {
       $stmt=$this->con->prepare("SELECT count(*) FROM $name");
    }
    $stmt->execute();
    //$stmt->store_result();
    //$result=$stmt->get_result();
    return $stmt->fetchColumn();
  }

  public function MojodiOzv(){

    $stmt=$this->con->prepare("SELECT SUM(mojodi) AS value_sum FROM users");
    $stmt->execute();
    //$result=$stmt->get_result();
  //  $row=$result->fetch_assoc();
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    $sum=$row['value_sum'];
    return $sum;

  }

  public function getMojodi(){
    $stmt=$this->con->prepare("SELECT * FROM sandogh");
    $stmt->execute();
    $data=array();
    $data=$stmt->fetch(PDO::FETCH_ASSOC);

    return $data;
  }

  public function backup(){
    $server_name='localhost';
    $username= 'root';
    $password='';
    $database_name='Sandoogh';
    $date_string= date("Ymd");

    $cmd = "mysqldump -h {$server_name} -u {$username} -p {$password} {$database_name} > {$date_string}_{$database_name}.sql";

    exec($cmd);

  }
}
 ?>
