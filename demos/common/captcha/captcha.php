<?php
class Captcha
{
    // 图像宽度
    protected $_width = 100;

    // 图像高度
    protected $_height = 40;

    // 随机字符范围
    protected $_code = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";

    // 字体文件
    protected $_font_file = './WhatsHappened.ttf';

    // 图像
    protected $_im;

    // 验证码
    protected $_captcha;

    public function __construct($width = null, $height = null)
    {
        $this->create($width, $height);
    }

    /**
     * 创建图像
     * @param string $width 宽度
     * @param string $height 高度
     */
    public function create($width, $height)
    {
        $this->_width = is_numeric($width) ? $width : $this->_width;
        $this->_height = is_numeric($height) ? $height : $this->_height;

        // 创建图像
        $im = imagecreatetruecolor($this->_width, $this->_height);

        // 填充底色
        $back = imagecolorallocate($im, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
        imagefill($im, 0, 0, $back);

        $this->_im = $im;
    }

    /**
     * 验证码混淆（加点和线）
     */
    public function moll()
    {
        $back = imagecolorallocate($this->_im, 0, 0, 0);
        for ($i = 0; $i < 30; $i++) {
            imagesetpixel($this->_im, mt_rand(0, $this->_width), mt_rand(0, $this->_height), $back);
        }
        imageline($this->_im, mt_rand(0, $this->_width), mt_rand(0, $this->_height), mt_rand(0, $this->_width), mt_rand(0, $this->_height), $back);
        imageline($this->_im, mt_rand(0, $this->_width), mt_rand(0, $this->_height), mt_rand(0, $this->_width), mt_rand(0, $this->_height), $back);
    }

    /**
     * 字符串验证码
     * @param int string $length    验证码字符串长度
     * @param int string $fontsize  验证码字体大小
     * @return $this
     */
    public function string($length = 4, $fontsize = 15)
    {
        $this->moll();
        $code = $this->_code;
        $captcha = '';
        for ($i = 0; $i < $length; $i++) {
            $string = $code[mt_rand(0, strlen($code) - 1)];
            $strColor = imagecolorallocate($this->_im, mt_rand(100, 150), mt_rand(100, 150), mt_rand(100, 150));
            imagettftext($this->_im, $fontsize, mt_rand(-10, 10), mt_rand(3, 10) + $i * (($this->_width - 5) / $length), $this->_height * 2 / 3, $strColor, $this->_font_file, $string);
            $captcha .= $string;
        }
        $this->_captcha = $captcha;
        return $this;
    }

    /**
     * 把验证码存入session
     */
    public function setSession()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['captcha_code'] = $this->_captcha;
    }

    /**
     * 输出验证码
     */
    public function show()
    {
        $this->setSession();
        header('Content-Type:image/png');
        imagepng($this->_im);
        imagedestroy($this->_im);
    }
}