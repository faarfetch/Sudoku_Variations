<?php

    class Utente {
        private $id;
        private $username;
        private $password;

        public function __construct($id, $username,$password) {
            $this->id = $id;    
            $this->username = $username;
            $this->password = $password;
        }

        public function getUsername() {
            return $this->username;
        }


        public function getId() {
            return $this->id;
        }

        public function gePassword() {
            return $this->password;
        }

        static public function parse($vettoreInfo)
        {
            return new Utente(
                $vettoreInfo["ID"],
                $vettoreInfo["Username"],
                $vettoreInfo["password"]
            );
        }

    }
?>