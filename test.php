#!/usr/bin/env php
<?php

function testingReadme()
{
    echo '## testing README'.PHP_EOL;
    
    $message = "SAFE";
    file_put_contents("readme", "Hello World");
    
    try {
        $image = new Imagick();
        $image->readImage('read.jpg');
        $image->setformat('png');
        $image->writeimage('readme.png');
    } catch (ImagickException $e) {
    } finally {
      if (file_exists('readme.png')) {
          $message = 'UNSAFE';
          unlink('readme.png');
      }
      unlink("readme");
    }
    echo $message.PHP_EOL.PHP_EOL;
    
}

function testingDelete()
{
    echo '## testing DELETE'.PHP_EOL;
    
    $message = "SAFE";
    touch("delme");
    
    try {
        $image = new Imagick('delete.jpg');
    } catch (ImagickException $e) {
    } finally {
      if (!file_exists('delme')) {
          $message = 'UNSAFE';
      } else {
            unlink('delme');
      }
    }
    echo $message.PHP_EOL.PHP_EOL;
    
}

function testingHttpWithLocalPort() {
    $port = rand(0, 32767) + 16384;
    echo "### testing HTTP with local port: ".$port.PHP_EOL;

    $message = "SAFE";
    
    system('printf \"HTTP/1.0 200 OK\n\n\" | nc -l '.$port.' > requestheaders 2>/dev/null &', $ret);
    if ($ret !== 0) {
        echo 'failed to listen on localhost:'.$port.PHP_EOL.PHP_EOL;
        return;
    } else {
        $contents = file_get_contents("localhost_http.jpg");
        file_put_contents('localhost_http1.jpg', preg_replace("/PORT/", $port, $contents));
        try {
            $image = new Imagick('localhost_http1.jpg');
        } catch(ImagickException $e) {
        } finally {
            unlink('localhost_http1.jpg');
            if (filesize('requestheaders') === 0) {
                $message = "UNSAFE";
            }
            system('echo | nc localhost '.$port.' 2>/dev/null 1>/dev/null');
            unlink('requestheaders');
        }
        echo $message.PHP_EOL.PHP_EOL;
    }
}

function testingHttpWithNonce() {
    $nonce = system('echo $RANDOM | md5sum | fold -w 8 | head -n 1');
    echo '### testing HTTP with nonce: '.$nonce.PHP_EOL;
    $ip = system('curl -q -s ifconfig.co');
    $contents = file_get_contents("http.jpg");
    file_put_contents('http1.jpg', preg_replace("/NONCE/", $nonce, $contents));
    try {
        $image = new Imagick('http1.jpg');
    } catch(ImagickException $e) {
    } finally {
        unlink('http1.jpg');
        system('curl -q -s "http://hacker.toys/dns?query='.$nonce.'.imagetragick" | grep -q '.$ip, $ret);
        if ($ret === 0) {
            $message = "UNSAFE";
        }
    }
    echo $message.PHP_EOL.PHP_EOL;

}

function testingRce1()
{
    echo '## testing RCE1'.PHP_EOL;

    $message = "SAFE";
    
    try {
        $image = new Imagick('rce1.jpg');
    } catch (ImagickException $e) {
    } finally {
      if (file_exists('rce1')) {
          $message = 'UNSAFE';
          unlink('rce1');
      }
    }
    echo $message.PHP_EOL.PHP_EOL;
}

function testingRce2()
{
    echo '## testing RCE2'.PHP_EOL;

    $message = "SAFE";
    
    try {
        $image = new Imagick('rce2.jpg');
    } catch (ImagickException $e) {
    } finally {
      if (file_exists('rce2')) {
          $message = 'UNSAFE';
          unlink('rce2');
      }
    }
    echo $message.PHP_EOL.PHP_EOL;
}

function testingMsl()
{
    echo '## testing MSL'.PHP_EOL;

    $message = "SAFE";
    
    try {
        $image = new Imagick('msl.jpg');
    } catch (ImagickException $e) {
    } finally {
      if (file_exists('msl.hax')) {
          $message = 'UNSAFE';
          unlink('msl.hax');
      }
    }
    echo $message.PHP_EOL.PHP_EOL;
}

testingReadme();
testingDelete();
testingHttpWithLocalPort();
testingHttpWithNonce();
testingRce1();
testingRce2();
testingMsl();
