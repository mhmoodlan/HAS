<?php

    class Authentication {

        /*
         *
         * Usage :
         *
         *      $id = 201;
         *      $auth = new Authentication($id);
         *      $token1 = $auth->getToken();
         *
         *
         *  # in other page :
         *      $checkAuth = new Authentication($id);
         *
         *      if($checkAuth->checkToken($retrieveToken)){
         *              // OK
         *      }else{
         *              // NO
         *      }
         *
         *
         *
         */



        private $id;

        public  function  __construct($id)
        {
            $this->id = $id;
        }

        public function getToken(){
            $token = '';
            $time = time();
//            $time = Authentication::encrypt($time);

            $token .=  (strrev(str_repeat(substr(md5($this->id), 16)
                    .substr(md5($this->id), 0, 15), 4)));

            $ptr = '';

            for($i = 0; $i < strlen($token); $i += 4){
                $ptr .= $token[$i];
            }


            $token = $time . '-' . $ptr;
            return $token;

        }


        public function checkToken($token){

            if($this->checkTimeToken($token) === TRUE) {
                    $t1 = $this->getHashFromToken($token);
                    $t2 = $this->getHashFromToken($this->getToken());


                    if($t1 === $t2){
                        return TRUE;
                    }else return FALSE;


            }else return FALSE;

        }

        private function checkTimeToken($token){
            $time1 = explode('-', $token);
            $current_time = time();

            $diff = $current_time - $time1[0];



            if($diff < 0) return FALSE;

            if($diff > 3600) {
                return FALSE;
            }

            return TRUE;
        }


        private function getHashFromToken($token){
            $t = explode('-', $token);
            return $t[1];
        }

        private static function encrypt($string){

        }
        private static function decrypt($string){

        }



        public static function isSafeID($id){
            $safe = (string)((int)($id));
            if($id != $safe) return FALSE; else return TRUE;
        }

    }







?>