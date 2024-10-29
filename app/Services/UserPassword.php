<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserPassword
{
  public function decryptUserPasswword($userId)
  {
    $decryptPwd = DB::connection('icss_db')->table('users')->selectRaw("
      case when len(user_pswd) <=0 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,len(user_pswd)),1)) + ascii('j') - ascii('n')) end+
      case when len(user_pswd) <=1 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-1<0 then 0 else len(user_pswd)-1 end ),1)) + ascii('n') - ascii('j')) end+
      case when len(user_pswd) <=2 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-2<0 then 0 else len(user_pswd)-2 end ),1)) + ascii('n') - ascii('n')) end+
      case when len(user_pswd) <=3 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-3<0 then 0 else len(user_pswd)-3 end ),1)) + ascii('8') - ascii('n')) end+
      case when len(user_pswd) <=4 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-4<0 then 0 else len(user_pswd)-4 end ),1)) + ascii('n') - ascii('8')) end+
      case when len(user_pswd) <=5 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-5<0 then 0 else len(user_pswd)-5 end ),1)) + ascii('v') - ascii('n')) end+
      case when len(user_pswd) <=6 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-6<0 then 0 else len(user_pswd)-6 end ),1)) + ascii('k') - ascii('v')) end+
      case when len(user_pswd) <=7 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-7<0 then 0 else len(user_pswd)-7 end ),1)) + ascii('m') - ascii('k')) end+
      case when len(user_pswd) <=8 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-8<0 then 0 else len(user_pswd)-8 end ),1)) + ascii('*') - ascii('m')) end+
      case when len(user_pswd) <=9 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-9<0 then 0 else len(user_pswd)-9 end ),1)) + ascii(')') - ascii('*')) end+
      case when len(user_pswd) <=10 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-10<0 then 0 else len(user_pswd)-10 end ),1)) + ascii('9') - ascii(')')) end+
      case when len(user_pswd) <=11 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-11<0 then 0 else len(user_pswd)-11 end ),1)) + ascii('0') - ascii('9')) end+
      case when len(user_pswd) <=12 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-12<0 then 0 else len(user_pswd)-12 end ),1)) + ascii('u') - ascii('0')) end+
      case when len(user_pswd) <=13 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-13<0 then 0 else len(user_pswd)-13 end ),1)) + ascii('8') - ascii('u')) end+
      case when len(user_pswd) <=14 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-14<0 then 0 else len(user_pswd)-14 end ),1)) + ascii('2') - ascii('8')) end+
      case when len(user_pswd) <=15 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-15<0 then 0 else len(user_pswd)-15 end ),1)) + ascii('j') - ascii('2')) end+
      case when len(user_pswd) <=16 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-16<0 then 0 else len(user_pswd)-16 end ),1)) + ascii('3') - ascii('j')) end+
      case when len(user_pswd) <=17 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-17<0 then 0 else len(user_pswd)-17 end ),1)) + ascii('9') - ascii('3')) end+
      case when len(user_pswd) <=18 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-18<0 then 0 else len(user_pswd)-18 end ),1)) + ascii('0') - ascii('9')) end+
      case when len(user_pswd) <=19 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-19<0 then 0 else len(user_pswd)-19 end ),1)) + ascii('m') - ascii('0')) end+
      case when len(user_pswd) <=20 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-20<0 then 0 else len(user_pswd)-20 end ),1)) + ascii('p') - ascii('m')) end+
      case when len(user_pswd) <=21 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-21<0 then 0 else len(user_pswd)-21 end ),1)) + ascii('=') - ascii('p')) end+
      case when len(user_pswd) <=22 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-22<0 then 0 else len(user_pswd)-22 end ),1)) + ascii('=') - ascii('=')) end+
      case when len(user_pswd) <=23 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-23<0 then 0 else len(user_pswd)-23 end ),1)) + ascii('4') - ascii('=')) end+
      case when len(user_pswd) <=24 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-24<0 then 0 else len(user_pswd)-24 end ),1)) + ascii('m') - ascii('4')) end+
      case when len(user_pswd) <=25 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-25<0 then 0 else len(user_pswd)-25 end ),1)) + ascii('k') - ascii('m')) end+
      case when len(user_pswd) <=26 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-26<0 then 0 else len(user_pswd)-26 end ),1)) + ascii('c') - ascii('k')) end+
      case when len(user_pswd) <=27 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-27<0 then 0 else len(user_pswd)-27 end ),1)) + ascii('n') - ascii('c')) end+
      case when len(user_pswd) <=28 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-28<0 then 0 else len(user_pswd)-28 end ),1)) + ascii('3') - ascii('n')) end+
      case when len(user_pswd) <=29 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-29<0 then 0 else len(user_pswd)-29 end ),1)) + ascii('4') - ascii('3')) end+
      case when len(user_pswd) <=30 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-30<0 then 0 else len(user_pswd)-30 end ),1)) + ascii('5') - ascii('4')) end+
      case when len(user_pswd) <=31 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-31<0 then 0 else len(user_pswd)-31 end ),1)) + ascii('n') - ascii('5')) end+
      case when len(user_pswd) <=32 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-32<0 then 0 else len(user_pswd)-32 end ),1)) + ascii('d') - ascii('n')) end+
      case when len(user_pswd) <=33 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-33<0 then 0 else len(user_pswd)-33 end ),1)) + ascii('e') - ascii('d')) end+
      case when len(user_pswd) <=34 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-34<0 then 0 else len(user_pswd)-34 end ),1)) + ascii('x') - ascii('e')) end+
      case when len(user_pswd) <=35 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-35<0 then 0 else len(user_pswd)-35 end ),1)) + ascii('t') - ascii('x')) end+
      case when len(user_pswd) <=36 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-36<0 then 0 else len(user_pswd)-36 end ),1)) + ascii('e') - ascii('t')) end+
      case when len(user_pswd) <=37 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-37<0 then 0 else len(user_pswd)-37 end ),1)) + ascii('r') - ascii('e')) end+
      case when len(user_pswd) <=38 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-38<0 then 0 else len(user_pswd)-38 end ),1)) + ascii('i') - ascii('r')) end+
      case when len(user_pswd) <=39 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-39<0 then 0 else len(user_pswd)-39 end ),1)) + ascii('4') - ascii('i')) end+
      case when len(user_pswd) <=40 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-40<0 then 0 else len(user_pswd)-40 end ),1)) + ascii('5') - ascii('4')) end+
      case when len(user_pswd) <=41 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-41<0 then 0 else len(user_pswd)-41 end ),1)) + ascii('i') - ascii('5')) end+
      case when len(user_pswd) <=42 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-42<0 then 0 else len(user_pswd)-42 end ),1)) + ascii('3') - ascii('i')) end+
      case when len(user_pswd) <=43 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-43<0 then 0 else len(user_pswd)-43 end ),1)) + ascii('j') - ascii('3')) end+
      case when len(user_pswd) <=44 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-44<0 then 0 else len(user_pswd)-44 end ),1)) + ascii('4') - ascii('j')) end+
      case when len(user_pswd) <=45 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-45<0 then 0 else len(user_pswd)-45 end ),1)) + ascii('i') - ascii('4')) end+
      case when len(user_pswd) <=46 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-46<0 then 0 else len(user_pswd)-46 end ),1)) + ascii(' ') - ascii('i')) end+
      case when len(user_pswd) <=47 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-47<0 then 0 else len(user_pswd)-47 end ),1)) + ascii('f') - ascii(' ')) end+
      case when len(user_pswd) <=48 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-48<0 then 0 else len(user_pswd)-48 end ),1)) + ascii(' ') - ascii('f')) end+
      case when len(user_pswd) <=49 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-49<0 then 0 else len(user_pswd)-49 end ),1)) + ascii('3') - ascii(' ')) end+
      case when len(user_pswd) <=50 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-50<0 then 0 else len(user_pswd)-50 end ),1)) + ascii('4') - ascii('3')) end+
      case when len(user_pswd) <=51 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-51<0 then 0 else len(user_pswd)-51 end ),1)) + ascii('i') - ascii('4')) end+
      case when len(user_pswd) <=52 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-52<0 then 0 else len(user_pswd)-52 end ),1)) + ascii('5') - ascii('i')) end+
      case when len(user_pswd) <=53 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-53<0 then 0 else len(user_pswd)-53 end ),1)) + ascii('4') - ascii('5')) end+
      case when len(user_pswd) <=54 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-54<0 then 0 else len(user_pswd)-54 end ),1)) + ascii('9') - ascii('4')) end+
      case when len(user_pswd) <=55 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-55<0 then 0 else len(user_pswd)-55 end ),1)) + ascii('4') - ascii('9')) end+
      case when len(user_pswd) <=56 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-56<0 then 0 else len(user_pswd)-56 end ),1)) + ascii('d') - ascii('4')) end+
      case when len(user_pswd) <=57 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-57<0 then 0 else len(user_pswd)-57 end ),1)) + ascii('&') - ascii('d')) end+
      case when len(user_pswd) <=58 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-58<0 then 0 else len(user_pswd)-58 end ),1)) + ascii('s') - ascii('&')) end+
      case when len(user_pswd) <=59 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-59<0 then 0 else len(user_pswd)-59 end ),1)) + ascii('j') - ascii('s')) end+
      case when len(user_pswd) <=60 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-60<0 then 0 else len(user_pswd)-60 end ),1)) + ascii('d') - ascii('j')) end+
      case when len(user_pswd) <=61 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-61<0 then 0 else len(user_pswd)-61 end ),1)) + ascii('h') - ascii('d')) end+
      case when len(user_pswd) <=62 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-62<0 then 0 else len(user_pswd)-62 end ),1)) + ascii('H') - ascii('h')) end+
      case when len(user_pswd) <=63 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-63<0 then 0 else len(user_pswd)-63 end ),1)) + ascii('U') - ascii('H')) end+
      case when len(user_pswd) <=64 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-64<0 then 0 else len(user_pswd)-64 end ),1)) + ascii('U') - ascii('U')) end+
      case when len(user_pswd) <=65 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-65<0 then 0 else len(user_pswd)-65 end ),1)) + ascii('I') - ascii('U')) end+
      case when len(user_pswd) <=66 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-66<0 then 0 else len(user_pswd)-66 end ),1)) + ascii('H') - ascii('I')) end+
      case when len(user_pswd) <=67 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-67<0 then 0 else len(user_pswd)-67 end ),1)) + ascii('j') - ascii('H')) end+
      case when len(user_pswd) <=68 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-68<0 then 0 else len(user_pswd)-68 end ),1)) + ascii('n') - ascii('j')) end+
      case when len(user_pswd) <=69 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-69<0 then 0 else len(user_pswd)-69 end ),1)) + ascii('L') - ascii('n')) end+
      case when len(user_pswd) <=70 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-70<0 then 0 else len(user_pswd)-70 end ),1)) + ascii('i') - ascii('L')) end+
      case when len(user_pswd) <=71 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-71<0 then 0 else len(user_pswd)-71 end ),1)) + ascii('s') - ascii('i')) end+
      case when len(user_pswd) <=72 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-72<0 then 0 else len(user_pswd)-72 end ),1)) + ascii('a') - ascii('s')) end+
      case when len(user_pswd) <=73 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-73<0 then 0 else len(user_pswd)-73 end ),1)) + ascii('n') - ascii('a')) end+
      case when len(user_pswd) <=74 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-74<0 then 0 else len(user_pswd)-74 end ),1)) + ascii('_') - ascii('n')) end+
      case when len(user_pswd) <=75 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-75<0 then 0 else len(user_pswd)-75 end ),1)) + ascii('j') - ascii('_')) end+
      case when len(user_pswd) <=76 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-76<0 then 0 else len(user_pswd)-76 end ),1)) + ascii('o') - ascii('j')) end+
      case when len(user_pswd) <=77 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-77<0 then 0 else len(user_pswd)-77 end ),1)) + ascii('s') - ascii('o')) end+
      case when len(user_pswd) <=78 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-78<0 then 0 else len(user_pswd)-78 end ),1)) + ascii('e') - ascii('s')) end+
      case when len(user_pswd) <=79 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-79<0 then 0 else len(user_pswd)-79 end ),1)) + ascii('2') - ascii('e')) end+
      case when len(user_pswd) <=80 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-80<0 then 0 else len(user_pswd)-80 end ),1)) + ascii('0') - ascii('2')) end+
      case when len(user_pswd) <=81 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-81<0 then 0 else len(user_pswd)-81 end ),1)) + ascii('0') - ascii('0')) end+
      case when len(user_pswd) <=82 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-82<0 then 0 else len(user_pswd)-82 end ),1)) + ascii('7') - ascii('0')) end+
      case when len(user_pswd) <=83 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-83<0 then 0 else len(user_pswd)-83 end ),1)) + ascii('+') - ascii('7')) end+
      case when len(user_pswd) <=84 then '' else nchar(ascii(LEFT(RIGHT(user_pswd,case when len(user_pswd)-84<0 then 0 else len(user_pswd)-84 end ),1)) + ascii('') - ascii('+')) end+
      '' as pwd,user_id,user_description,usergrp_id,net_userid,user_pswd
      ")->where('user_id', $userId);
    $user = $decryptPwd->first();
    
    return $user;
  }
}