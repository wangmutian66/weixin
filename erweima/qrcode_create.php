<?php
/**
 * Created by PhpStorm.
 * User: txkj
 * Date: 2018/8/23
 * Time: 14:14
 */
include "phpqrcode/qrlib.php";
/**
 * $text 文本内容
 * $outfile 将二维码图片输出的路径
 *
 */
//png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false)

QRcode::png('abc');