<?php
/**
 * @note session 资源转存
 */


class fileSessionHandler implements SessionHandlerInterface{

    private $savePath;

    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    public function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}


class session {
	
	public function __construct(){
		$this->_init();
    }

    private function _init(){
		$handler = new fileSessionHandler();
		session_set_save_handler(
		    array($handler, 'open'),
		    array($handler, 'close'),
		    array($handler, 'read'),
		    array($handler, 'write'),
		    array($handler, 'destroy'),
		    array($handler, 'gc')
		);

		register_shutdown_function('session_write_close');

		/**
		 * session.save_path 定义了传递给存储处理器的参数。
		 * 如果选择了默认的 files 文件处理器，则此值是创建文件的路径。
		 * @default /tmp。
		 */
		ini_set('session.save_path','/tmp');

		/**
		 * session.name 指定会话名以用做 cookie 的名字。只能由字母数字组成。
		 * @default PHPSESSID
		 */
		ini_set('session.name','MYSESSID');

		/**
		 * GC（garbage collection 垃圾回收）进程的概率。
		 * 
		 * session.gc_probability 与 session.gc_divisor 合起来定义了在每个会话初始化时启动 gc。
		 * 此概率用 gc_probability/gc_divisor 计算得来。
		 * 例如 1/100 意味着在每个请求中有 1% 的概率启动 gc 进程。
		 * 
		 * @default gc_probability	1
		 *          gc_divisor		100
		 */
		ini_set('session.gc_probability', 100);
		ini_set('session.gc_divisor', 100);

		/**
		 * session.gc_maxlifetime 指定过了多少秒之后数据就会被视为“垃圾”并被清除。
		 * @default 1440
		 *
		 * 假如有100条过期记录，GC概率为1%，则有99%几率不会启动。
		 * 故，回收概率的大小和清除SESSION（释放所占空间）几率成正比。
		 */
		ini_set('session.gc_maxlifetime', 3600*2);

		/**
		 * session.hash_function 允许指定用于生成会话ID的哈希算法。
		 * '0'表示MD5（128位）
		 * '1'表示SHA-1（160位）。
		 * 自PHP5.3.0，也可以指定任意的由散列扩展名（如果可用的话），如SHA512或漩涡所提供的算法。
		 * 可以用hash_algos（）函数获得支持的算法的完整列表。
		 * @default 0
		 */
		
		ini_set('session.hash_function', 1);
		/**
		 * session.hash_bits_per_character 允许定义转换二进制hash数据，有多少位来存储每个字符。
		 * '4'（0-9, a-f）
		 * '5'（0-9, a-v）
		 * '6'（0-9, a-z, A-Z, "-", ","）
		 * @default 4
		 */
		ini_set('session.hash_bits_per_character', 6);

		session_start();
    }
}


$a = new session();
$_SESSION['first_name'] = "jack";

var_dump($_SESSION);