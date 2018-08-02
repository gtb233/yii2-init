<?php
/**
 * 图片压缩类
 */
class ImageZoom
{
	/**
	 * php缩略图函数：
	 * 等比例无损压缩，可填充补充色
	 * 样例：imagezoom('3.png', '222.jpg', 1000, 750, '#FFFFFF'); //4:3
	 * 主持格式：
	 * bmp 、jpg 、gif、png
	 * @param $srcimage	要缩小的图片
	 * @param $dstimage	要保存的图片
	 * @param $dst_width	缩小宽
	 * @param $dst_height  	缩小高
	 * @param $backgroundcolor   补充色 如：#FFFFFF 支持 6位 不支持3位
	 */
	public function imageToZoom( $srcimage, $dstimage, $dst_width, $dst_height, $backgroundcolor ) {
		// 中文件名乱码
		if ( PHP_OS == 'WINNT' ) {
			$srcimage = iconv('UTF-8', 'GBK', $srcimage);
			$dstimage = iconv('UTF-8', 'GBK', $dstimage);
		}
		$dstimg = imagecreatetruecolor( $dst_width, $dst_height );//新建一个真彩色图像
		$color = imagecolorallocate($dstimg
				, hexdec(substr($backgroundcolor, 1, 2))
				, hexdec(substr($backgroundcolor, 3, 2))
				, hexdec(substr($backgroundcolor, 5, 2))
		);//第一次对 imagecolorallocate() 的调用会给基于调色板的图像填充背景  例：imagecolorallocate($im, 255, 255, 255);
		imagefill($dstimg, 0, 0, $color);// 区域填充 
		if ( !$arr=getimagesize($srcimage) ) {
			echo "要生成缩略图的文件不存在";
			exit;
		}
		$src_width = $arr[0];
		$src_height = $arr[1];
		$srcimg = null;
		$method = $this->getcreatemethod( $srcimage );
		if ( $method ) {
			eval( '$srcimg = ' . $method . ';' );
		}
		$dst_x = 0;
		$dst_y = 0;
		$dst_w = $dst_width;
		$dst_h = $dst_height;
		if ( ($dst_width / $dst_height - $src_width / $src_height) > 0 ) {
			$dst_w = $src_width * ( $dst_height / $src_height );
			$dst_x = ( $dst_width - $dst_w ) / 2;
		} elseif ( ($dst_width / $dst_height - $src_width / $src_height) < 0 ) {
			$dst_h = $src_height * ( $dst_width / $src_width );
			$dst_y = ( $dst_height - $dst_h ) / 2;
		}
		#将一幅图像中的一块正方形区域拷贝到另一个图像中，平滑地插入像素值，因此，尤其是，减小了图像的大小而仍然保持了极大的清晰度。
		imagecopyresampled($dstimg, $srcimg, $dst_x	, $dst_y, 0, 0, $dst_w, $dst_h, $src_width, $src_height);
		// 保存格式
		$arr = array(
				'jpg' => 'imagejpeg'
				, 'jpeg' => 'imagejpeg'
				, 'png' => 'imagepng'
				, 'gif' => 'imagegif'
				, 'bmp' => 'imagebmp'
		);
		$adrStr = explode('.', $dstimage );
		$suffix = strtolower( array_pop($adrStr ) );
		if (!in_array($suffix, array_keys($arr)) ) {
			echo "保存的文件名错误";
			exit;
		} else {
			eval( $arr[$suffix] . '($dstimg, "'.$dstimage.'");' );
		}
		imagejpeg($dstimg, $dstimage);
		imagedestroy($dstimg);
		imagedestroy($srcimg);
	}
	
	protected function getCreateMethod( $file ) {
		$arr = array(
				'474946' => "imagecreatefromgif('$file')"
				, 'FFD8FF' => "imagecreatefromjpeg('$file')"
				, '424D' => "imagecreatefrombmp('$file')"
				, '89504E' => "imagecreatefrompng('$file')"
		);
		$fd = fopen( $file, "rb" );
		$data = fread( $fd, 3 );
		$data = $this->str2hex( $data );
		if ( array_key_exists( $data, $arr ) ) {
			return $arr[$data];
		} elseif ( array_key_exists( substr($data, 0, 4), $arr ) ) {
			return $arr[substr($data, 0, 4)];
		} else {
			return false;
		}
	}
	
	protected function str2hex( $str ) {
		$ret = "";
		for( $i = 0; $i < strlen( $str ) ; $i++ ) {
			$ret .= ord($str[$i]) >= 16 ? strval( dechex( ord($str[$i]) ) )
			: '0'. strval( dechex( ord($str[$i]) ) );
		}
		return strtoupper( $ret );
	}
	
	/**
	 * BMP 创建函数 php本身无
	 * 暂时未研究
	 */ 
	private function imagecreatefrombmp($filename)
	{
		if (! $f1 = fopen($filename,"rb")) return FALSE;
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		if ($FILE['file_type'] != 19778) return FALSE;
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
				'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
				'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
		$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
		if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] = 4-(4*$BMP['decal']);
		if ($BMP['decal'] == 4) $BMP['decal'] = 0;
		$PALETTE = array();
		if ($BMP['colors'] < 16777216)
		{
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		}
		$IMG = fread($f1,$BMP['size_bitmap']);
		$VIDE = chr(0);
		$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		$P = 0;
		$Y = $BMP['height']-1;
		while ($Y >= 0)
		{
			$X=0;
			while ($X < $BMP['width'])
			{
				if ($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
				elseif ($BMP['bits_per_pixel'] == 16)
				{
					$COLOR = unpack("n",substr($IMG,$P,2));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 8)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 4)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 1)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if (($P*8)%8 == 0) $COLOR[1] = $COLOR[1] >>7;
					elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
					elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
					elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
					elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
					elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
					elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
					elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				else
					return FALSE;
				imagesetpixel($res,$X,$Y,$COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		}
		fclose($f1);
		return $res;
	}
	/**
	 *  BMP 保存函数，php本身无 
	 *  还未看
	 */
	private function imagebmp ($im, $fn = false)
	{
		if (!$im) return false;
		if ($fn === false) $fn = 'php://output';
		$f = fopen ($fn, "w");
		if (!$f) return false;
		$biWidth = imagesx ($im);
		$biHeight = imagesy ($im);
		$biBPLine = $biWidth * 3;
		$biStride = ($biBPLine + 3) & ~3;
		$biSizeImage = $biStride * $biHeight;
		$bfOffBits = 54;
		$bfSize = $bfOffBits + $biSizeImage;
		fwrite ($f, 'BM', 2);
		fwrite ($f, pack ('VvvV', $bfSize, 0, 0, $bfOffBits));
		fwrite ($f, pack ('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));
		$numpad = $biStride - $biBPLine;
		for ($y = $biHeight - 1; $y >= 0; --$y)
		{
			for ($x = 0; $x < $biWidth; ++$x)
			{
				$col = imagecolorat ($im, $x, $y);
				fwrite ($f, pack ('V', $col), 3);
			}
			for ($i = 0; $i < $numpad; ++$i)
				fwrite ($f, pack ('C', 0));
		}
		fclose ($f);
		return true;
	}
}