<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Server;
use phpseclib\Net\SSH2 as SSH;
use phpseclib\Net\SFTP as Net_SFTP;

class ServersController extends Controller
{

    
    public function index() {
        $servers = Server::all();
        // return $servers;
        return view('admin1.server', compact('servers'));
    }

    public function CreatNewServer() {
        
        // $servers = Server::all();
        return view('admin1.create-server');
    }
    public function CreatNewServer_Post(Request $request) {
        echo "hello";
        $USER='mabrouk2288';
        $PASS='d';
        // $creat_user="sudo pam-auth-update --package |  sudo mount -o remount,rw / | sudo chmod 640 /etc/shadow | sudo useradd -m -s /bin/bash ".strval($USER)." | echo ".strval($USER).":".strval($PASS)."|sudo chpasswd |sudo usermod -aG sudo ".strval($USER) ;

        
        $ssh = New SSH($request->IP, $request->Port);
        if(!$ssh->login($request->username, $request->password)) {
            abort(500);
        }
        echo "vv";
        // SFTP connection

        $sftp = new Net_SFTP($request->IP);
        if (!$sftp->login($request->username, $request->password)) {
            exit('bad login');
        }
        // return 'ok';

        echo "sftp download";

        $files = array("default", "index.php","cipi_dbroot","default","haget.conf","jail","motd","nginx.conf","nodesource.list","php_fpm","phpfpm.conf","sites-available_default");
        foreach ($files as $key => $file) {
            $sftp->put("upload/".$file,"upload/".$file,Net_SFTP::SOURCE_LOCAL_FILE);
        }



        // return "Ok";

        $response = $ssh->exec("lsb_release -d | grep 'Ubuntu 20';");
        if (!str_contains($response, 'Ubuntu 20')){
            return "You shoud have Ubuntu 20 in your machine";
        }

        $response = $ssh->exec("id -u");
        // return $response;
        if ($response!=0){
            return "You shoud have root access";
        }

        $serv               = new Server();
        $serv->name        = $request->name_server;
        $serv->ip      = $request->IP;
        $serv->username     = $request->username;
        $serv->password     = $request->password;
        $serv->status       = 1;

        $serv->save();
 
        return redirect('server')->with([
            'message' => 'alert-success', 'Server '.$request->name.' has been created!',
            'message_important' => true
        ]);

        
        // Update apt-get

        $response = $ssh->exec("sudo apt-get update",function($output) {
            echo $output;
            echo '/n';
        });
        // Installing Requirement Package
        $packages=" sudo apt-get -y install nano rpl sed zip unzip openssl expect dirmngr apt-transport-https lsb-release ca-certificates dnsutils dos2unix zsh htop curl wget software-properties-common fail2ban nginx php-fpm php-common php-mbstring php-mysql php-xml php-zip php-bcmath php-imagick mysql-server redis-server git supervisor nodejs npm yarn php-curl";
        $response = $ssh->exec($packages, function($output) {
            echo $output;
            echo '/n';
        });

        $response = $ssh->exec("sudo apt-get -y autoremove", function($output) {
            // return "hello";
            echo $output;
            echo '/n';
        });

        // Craeate New User
        $response = $ssh->exec("sudo pam-auth-update --package");
        $response = $ssh->exec("sudo mount -o remount,rw /");
        $response = $ssh->exec("sudo chmod 640 /etc/shadow");
        $response = $ssh->exec("sudo useradd -m -s /bin/bash ".strval($USER));
        $response = $ssh->exec("echo ".strval($USER).":".strval($PASS)."|sudo chpasswd");
        $response = $ssh->exec("sudo usermod -aG sudo ".strval($USER));

        // firewall config
        $response = $ssh->exec("sudo ufw allow ssh");
        $response = $ssh->exec("sudo ufw allow http");
        $response = $ssh->exec("sudo ufw allow https");
        $response = $ssh->exec("Nginx Full");
        $response = $ssh->exec("ufw --force enable");

        // nginx config
        $response = $ssh->exec("sudo systemctl start nginx.service");
        $response = $ssh->exec('sudo rpl -i -w "http {" "http { limit_req_zone \$binary_remote_addr zone=one:10m rate=1r/s; fastcgi_read_timeout 300;" /etc/nginx/nginx.conf');
        $response = $ssh->exec("sudo systemctl enable nginx.service");

        // redis config
        $response = $ssh->exec('sudo rpl -i -w "supervised no" "supervised systemd" /etc/redis/redis.conf');
        $response = $ssh->exec("sudo systemctl restart redis.service");

        // encrypt
        
        $response = $ssh->exec("sudo snap install --beta --classic certbot");

        // install composer

        $response = $ssh->exec("curl -sS https://getcomposer.org/installer -o composer-setup.php");
        $response = $ssh->exec("unlink('composer-setup.php');");
        $response = $ssh->exec("sudo php composer-setup.php");
        $response = $ssh->exec("sudo mv composer.phar /usr/local/bin/composer");
        $response = $ssh->exec("sudo composer config --global repo.packagist composer https://packagist.org");

        // restart supervisor
        $response = $ssh->exec("service supervisor restart");

        // config postfix

        $response = $ssh->exec("sudo DEBIAN_FRONTEND=noninteractive apt-get install -yq postfix");

        // PHP My Admin

        $response = $ssh->exec("composer create-project phpmyadmin/phpmyadmin /var/www/html/pma");
        $response = $ssh->exec("sudo mkdir /var/www/html/pma/tmp/");
        $response = $ssh->exec("sudo chmod 777 /var/www/html/pma/tmp/");
        $response = $ssh->exec("sudo mv /var/www/html/pma/config.sample.inc.php /var/www/html/pma/config.inc.php");
        $response = $ssh->exec('sudo rpl -i -w "["blowfish_secret"] = "";" "["blowfish_secret"] = "M12SQBq5JKVGA0qZ4ZhPBwfmb0hBYkMA";" /var/www/html/pma/config.inc.php');

        
        // postfix
        $response = $ssh->exec("sudo DEBIAN_FRONTEND=noninteractive apt-get install -yq postfix");


        // return 'Ok';


        

    
    }

    public function api() {
        return Server::orderBy('name')->orderBy('ip')->where('status', 2)->get();
    }


    public function get($servercode) {
        $server = Server::where('servercode', $servercode)->with('applications')->firstOrFail();
        return view('server', compact('server'));
    }


    public function create(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'ip' => 'required'
        ]);
        if($request->ip == $request->server('SERVER_ADDR')) {
            $request->session()->flash('alert-error', 'You can\'t install a client server into the same Cipi Server!');
            return redirect('/servers');
        }
        Server::create([
            'name'      => $request->name,
            'provider'  => $request->provider,
            'location'  => $request->location,
            'ip'        => $request->ip,
            'port'      => 22,
            // 'username'  => 'cipi',
            'password'  => sha1(uniqid().microtime().$request->ip),
            'dbroot'    => sha1(microtime().uniqid().$request->name),
            'servercode'=> sha1(uniqid().$request->name.microtime().$request->ip)
        ]);
        $request->session()->flash('alert-success', 'Server '.$request->name.' has been created!');
        return redirect('/servers');
    }


    public function changeip(Request $request) {
        $this->validate($request, [
            'servercode' => 'required',
            'ip'         => 'required'
        ]);
        $server = Server::where('servercode', $request->servercode)->first();
        if($request->ip == $request->server('SERVER_ADDR')) {
            $request->session()->flash('alert-error', 'You can\'t setup the same Cipi IP!');
            return redirect('/servers');
        }
        $server->ip = $request->input('ip');
        $server->save();
        $request->session()->flash('alert-success', 'The IP of server '.$server->name.' has been updated!');
        return redirect('/servers');
    }


    public function changename(Request $request) {
        $this->validate($request, [
            'servercode' => 'required',
            'name'       => 'required'
        ]);
        $server = Server::where('servercode', $request->servercode)->first();
        $server->name = $request->input('name');
        $server->save();
        $request->session()->flash('alert-success', 'The name of server '.$server->ip.' has been updated!');
        return redirect('/servers');
    }


    public function destroy(Request $request) {
        $this->validate($request, [
            'servercode' => 'required',
        ]);
        $server = Server::where('servercode', $request->servercode)->firstOrFail();
        $server->delete();
        $request->session()->flash('alert-success', 'Server '.$server->name.' has been deleted!');
        return redirect('/servers');
    }


    public function reset($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $pass = sha1(uniqid().microtime().$server->ip);
        $ssh->setTimeout(360);
        $response = $ssh->exec('echo '.$server->password.' | sudo -S sudo sh /cipi/root.sh -p '.$pass);
        if(strpos($response, '###CIPI###') === false) {
            abort(500);
        }
        $response = explode('###CIPI###', $response);
        if(strpos($response[1], 'Ok') === false) {
            abort(500);
        }
        $server->password = $pass;
        $server->save();
        return $pass;
    }

    public function nginx($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo systemctl restart nginx.service');
        return 'OK';
    }

    public function php($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo service php7.4-fpm restart');
        $ssh->exec('sudo service php7.3-fpm restart');
        $ssh->exec('sudo service php-fpm restart');
        return 'OK';
    }

    public function mysql($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo service mysql restart');
        return 'OK';
    }


    public function redis($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo systemctl restart redis.service');
        return 'OK';
    }


    public function supervisor($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('service supervisor restart');
        return 'OK';
    }

}
