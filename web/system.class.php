<?php
    class System {
        /**
         * @var string
         */
        public string $rootDir;
        /**
         * @var string
         */
        private string $compilerName;
        /**
         * Setting a location where all the scripts are located at
         * 
         * @param string $path  the path to the scripts
         * @return void
         */
        public function setRootDir(string $path): void{
            $this->rootDir = $path;
        }
        /**
         *  Checking if the compiler is already running
         * 
         * @return bool 
         */
        private function isRunning(): bool{
            $cmd = shell_exec("ps aux | egrep '{$this->compilerName}$' | grep -v grep");
            if (!empty($cmd)) return true;
            return false;
        }
        /**
         * Getting the statuss of the compiler process
         */
        public function getStatuss(){
            if ($this->isRunning()){
                echo json_encode(["statuss" => "C_ON"]);
                return;
            }
            echo json_encode(["statuss" => "C_OFF"]);
        }
        /**
         * Starting a compiler
         * 
         * @return void
         */
        public function startCompiler(): void{
            if (!$this->isRunning()){
                // updating the csv files every time when compiler is starting
                $this->updateCsvFiles();
                $cmd = shell_exec("cd {$this->rootDir}; ./start.sh");
                if (strpos("Starting", $cmd) !== 0) {
                    echo json_encode(["statuss" => "C_STARTED"]);
                }
                return;
            }
            echo json_encode(["statuss" => "C_RUNNING"]);
        }
        /**
         * Killing a compiler
         * 
         * @return void
         */
        public function killCompiler(){
            if (!$this->isRunning()) return;
            // getting a pid of the process
            $pid = $this->getPID();
            // killing a process
            shell_exec("kill -9 {$pid}");
            // if the process is still running, printing out message, that we failed
            if ($this->isRunning()){
                echo json_encode(["statuss" => "C_KILL_ERROR"]);
                return;
            }
            // if the process is killed, printing out that we successfuly killed it
            echo json_encode(["statuss" => "C_KILLED"]);
        }
        /**
         * Downloading an updated files from the FTP server
         * 
         * @return void
         */
        private function updateCsvFiles(){
            $credentials = (object)[
                "host" => "ftp.autopartner.dev",
                "user" => "3103900",
                "pass" => "gCWXKB",
            ];
            // files to download
            $files = ["3103900.csv", "3103900_KAUCJE.csv", "INDEKS_PARAMETR.csv", "STANY.csv",];
            // connecting to the ftp server
            $conn = ftp_connect($credentials->host);
            // logging in ftp server
            if (!ftp_login($conn, $credentials->user, $credentials->pass)){
                print "Connection to the FTP server failed...";
                return;
            }
            // turning on passive mode
            ftp_pasv($conn, true);
            // trying to download file from ftp server
            foreach ($files as $file_name) {
                // saving the files in the compiler root directory
                $local = sprintf("%s/%s", $this->rootDir, $file_name);
                if (!ftp_get($conn, $local, $file_name, FTP_BINARY)) {
                    print "Failed to get $file_name...";
                }
            }
            // closing the connection
            ftp_close($conn);
            echo json_encode(["CSV_UPDATED"]);
        }
        /**
         * Setting a compiler's file name
         * 
         * @param string $name  Compiler file name
         * @return void
         */
        public function setCompilerName(string $name): void{
            $this->compilerName = $name;
        }
        /**
         * Getting a Pid of the compiler's process
         * 
         * @return int
         */
        private function getPID(): int{
            return explode(" ", shell_exec("ps aux | egrep '{$this->compilerName}$' | grep -v grep"))[1];
        }
        /**
         * Downloading a compiled csv file
         * 
         * @return void
         */
        public function downloadFile(){
            $filepath = sprintf("%s/compiled.csv", $this->rootDir);
            if (!file_exists($filepath)) {
                echo json_encode(["statuss" => "D_NOFILE", "message" => sprintf("File %s does not exists", basename($filepath))]);
                return;
            }
            header("Content-Description: File Transfer");
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; " . sprintf("filename=%s", basename($filepath)));
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($filepath));
            flush();
            readfile($filepath);
        }
    }