<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ldap {

    private $CI;
    private $connHandle;
    private $ldapParams;

    function __construct() {
        $this->CI = &get_instance();
        $this->CI->config->load('ldap');
        $this->ldapParams = $this->CI->config->item('ldap');
        $this->connHandle = ldap_connect($this->ldapParams['host']) or die("Nie mozna nawiazac polaczenia z ".$this->ldapParams['host']);
        ldap_set_option($this->connHandle, LDAP_OPT_PROTOCOL_VERSION, 3);
    }

    public function login($dn, $pass, $type){
        $ldap_bind = @ldap_bind($this->connHandle, $dn, $pass);        
        if ($ldap_bind) {
            $result = ldap_search($this->connHandle, $dn, 'objectClass=*');
            $entry_data = @ldap_get_entries($this->connHandle, $result);
            $data['sn'] = $entry_data[0]['cn'][0];
            $data['type'] = $type;
            $data['logged_in'] = 'yes';
            $data['login'] =  $entry_data[0]['uid'][0];
            $data['pass'] = $pass;
            $data['dostepDo'] = $entry_data[0]['dostepdo'];
            $this->CI->session->set_userdata($data);
        }
        else {
            throw new Exception("Podane dane są nieprawidłowe !");
        }
    }

    public function changePassword($dn,$from_admin,$new_pass,$flag,$old_pass=null){
		//Jeżeli $flag = 3 zmieniamy oba hasła, 1 - tylko LDAP, 2 - tylko WIFI
    	$passChangeErrors = array();
		if(strlen($new_pass) < 8){$passChangeErrors[] = "Minimalna długość hasła to 8 znaków";}
		if(!preg_match('/[a-z]/', $new_pass) || !preg_match('/[A-Z]/', $new_pass)){$passChangeErrors[] = "Hasło musi zawierać małą i dużą literę";}
		if(!preg_match('/\d+/', $new_pass) || !preg_match('/.[!,@,#,$,%,^,&,*,?,_,~,-,(,),.]/', $new_pass)){$passChangeErrors[] = "Hasło musi zawierać cyfrę i znak specjalny";}
		if(count($passChangeErrors) > 0){ return $passChangeErrors; }





        $ldap_bind = false;
        if($from_admin == "1")
            $ldap_bind = @ldap_bind($this->connHandle, $this->ldapParams['adminDn'], $this->ldapParams['adminPass']);
        elseif($from_admin == "0")
            $ldap_bind = @ldap_bind($this->connHandle, $dn, $old_pass);

        if ($ldap_bind) {

        	if($flag==3 || $flag==1){
        		//Zmiana UKP
				$hashed_passwd = '{SHA}'.base64_encode(pack("H*",sha1($new_pass)));
				$entry['userPassword'] = $hashed_passwd;
				if(@ldap_modify($this->connHandle, $dn, $entry)){
					$passChangeErrors[] = "Hasło UKP(S) zostało zmienione poprawnie.";
				}
				else{
					ldap_unbind($this->connHandle);
					$passChangeErrors[] = "Błąd podczas zmiany hasła UKP(S) w bazie danych";
				}
			}

			if($flag==3 || $flag==2){
        		//Zmiana WIFI
				$hashed_passwd_w = strtoupper(bin2hex(mhash(MHASH_MD4,iconv('UTF-8','UTF-16LE',$new_pass))));
				$entry_w['sambaNTPassword'] = $hashed_passwd_w;
				if(@ldap_modify($this->connHandle, $dn, $entry_w)){
					$passChangeErrors[] = "Hasło WI-FI zostało zmienione poprawnie.";
				}
				else{
					ldap_unbind($this->connHandle);
					$passChangeErrors[] = "Błąd podczas zmiany hasła WIFI w bazie danych";
				}
			}

				return $passChangeErrors;


        }
        else {
            ldap_unbind($this->connHandle);
			$passChangeErrors[] = "Brak połączenia z bazą LDAP!";
			return $passChangeErrors;
        }
    }

    public function changeDostepDo($dn, $action, $value){
        $ldap_bind = @ldap_bind($this->connHandle, $this->ldapParams['adminDn'], $this->ldapParams['adminPass']);
        if ($ldap_bind) {
            $entry['dostepDo'] = $value;
            if($action == 'remove'){
                if(ldap_mod_del($this->connHandle, $dn, $entry))
                    return true;
                else
                    throw new Exception();
            }
            elseif($action == 'add'){
                if(ldap_mod_add($this->connHandle, $dn, $entry))
                    return true;
                else
                    throw new Exception();
            }
        }
        else
            throw new Exception();
    }
    
    public function getDostepDo($ou, $login){
        $dn = "uid=$login,ou=$ou,dc=uek,dc=krakow,dc=pl";
        $ldap_bind = @ldap_bind($this->connHandle, $this->ldapParams['adminDn'], $this->ldapParams['adminPass']);
        if ($ldap_bind) {
            $result = @ldap_search($this->connHandle, $dn, 'objectClass=*');
            $entry_data = @ldap_get_entries($this->connHandle, $result);
            return @$entry_data[0]['dostepdo'];
        }
    }

    public function countUsers($ou){
        $ldap_bind = @ldap_bind($this->connHandle, $this->ldapParams['adminDn'], $this->ldapParams['adminPass']);
        if($ldap_bind){
            $result = @ldap_search($this->connHandle, $ou, 'objectClass=*');
            $entry_data = @ldap_get_entries($this->connHandle, $result);
            return $entry_data['count'] - 1;
        }
    }



/*Find person by uid nazwisko album number dn*/
	public function getPersonObject($query){
		$data = array();
		$filter = "(&(|(uid=".$query."*)(nazwisko=".$query."*)(pleduPersonstudentNumber=".$query."*))(|(ou:dn:=Kadry)(ou:dn:=Umowy)(ou:dn:=Dziekanat)))";
		$justthese = array("uid", "objectclass", "dn", "cn", "pesel");
		$ldap_bind = @ldap_bind($this->connHandle, $this->ldapParams['adminDn'], $this->ldapParams['adminPass']);
		if($ldap_bind) {

			if (strpos($query, ',dc=uek,dc=krakow,dc=pl') !== false){
				//if query is a dn
				$sr = ldap_read($this->connHandle, $query, "(objectclass=*)", $justthese);

			}else{
				//if query is anything else
				$sr = ldap_search($this->connHandle, "dc=uek,dc=krakow,dc=pl", $filter, $justthese);
			}


			$entry_data = ldap_get_entries($this->connHandle, $sr);

			if($entry_data['count']===0){
				$data['error'] = true;
				return $data;
			}else{
				for($i=0;$i<$entry_data['count'];$i++){
					$data[$i]['uid'] = isset($entry_data[$i]['uid'][0]) ? $entry_data[$i]['uid'][0] : '';
					$data[$i]['pesel'] = isset($entry_data[$i]['pesel'][0]) ? $entry_data[$i]['pesel'][0] : '';
					$data[$i]['cn'] = isset($entry_data[$i]['cn'][0]) ? $entry_data[$i]['cn'][0] : '';
					$data[$i]['dn'] = isset($entry_data[$i]['dn']) ? $entry_data[$i]['dn'] : '';
					$data[$i]['objectclass'] = isset($entry_data[$i]['objectclass'][0]) ? $entry_data[$i]['objectclass'][0] : '';
				}

				return $data;
			}

		}else{
			throw new Exception();
		}

	}



}
