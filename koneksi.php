<?php
session_start();
$configFile=__DIR__.'/config/database.php';
if (!is_file($configFile)) { http_response_code(500); exit('Konfigurasi belum dibuat. Salin config/database.example.php ke config/database.php.'); }
$c=require $configFile;
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try { $koneksi=new mysqli($c['host'],$c['user'],$c['password'],$c['database']); $koneksi->set_charset('utf8mb4'); }
catch (mysqli_sql_exception $e) { http_response_code(500); exit('Koneksi database gagal. Periksa config/database.php.'); }
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function money($n){return 'Rp '.number_format((float)$n,0,',','.');}
function csrf(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function verify_csrf(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')) {http_response_code(403);exit('Token keamanan tidak valid.');}}
function redirect($url){header('Location: '.$url);exit;}
function cart_count(){return array_sum(array_map('intval',$_SESSION['cart']??[]));}
function cart_data($db){$cart=$_SESSION['cart']??[];if(!$cart)return [[],0];$rows=[];$total=0;$stmt=$db->prepare('SELECT id_produk,nama_produk,harga,stok,gambar FROM produk WHERE id_produk=?');foreach($cart as $id=>$qty){$stmt->bind_param('i',$id);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();if(!$row)continue;$row['jumlah']=(int)$qty;$row['subtotal']=$row['harga']*$qty;$total+=$row['subtotal'];$rows[]=$row;}return [$rows,$total];}
function require_admin(){if(empty($_SESSION['admin_id']))redirect('../admin/login.php');}
