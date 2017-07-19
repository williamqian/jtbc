<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class image
  {
    public static function resizeImage($argURL1, $argURL2, $argWidth, $argHeight, $argScale = 0, $argRotate = 0, $argQuality = 100)
    {
      $bool = false;
      $URL1 = $argURL1;
      $URL2 = $argURL2;
      $scale = base::getNum($argScale, 0);
      $width = base::getNum($argWidth, 0);
      $height = base::getNum($argHeight, 0);
      $rotate = base::getNum($argRotate, 0);
      $quality = base::getNum($argQuality, 0);
      if (!base::isEmpty($URL1) && !base::isEmpty($URL2) && $width != 0 && $height != 0)
      {
        $img = null;
        $imageType = base::getLRStr($URL1, '.', 'right');
        if ($imageType == 'jpg' || $imageType == 'jpeg') $img = @imagecreatefromjpeg($URL1);
        elseif ($imageType == 'gif') $img = @imagecreatefromgif($URL1);
        elseif ($imageType == 'png') $img = @imagecreatefrompng($URL1);
        if ($img && function_exists('imagecopyresampled'))
        {
          $imageX = 0;
          $imageY = 0;
          $imageSize = getImageSize($URL1);
          $imageWidth = $imageSize[0];
          $imageHeight = $imageSize[1];
          $newImageWidth = $imageWidth;
          $newImageHeight = $imageHeight;
          if ($width == -1) $width = $imageWidth;
          if ($height == -1) $height = $imageHeight;
          if ($scale == 0)
          {
            $scNum1 = $imageWidth / $width;
            $scNum2 = $imageHeight / $height;
            if ($scNum1 >= $scNum2)
            {
              $newImageWidth = $scNum2 * $width;
              $imageX = round(abs($imageWidth - $newImageWidth) / 2);
            }
            else
            {
              $newImageHeight = $scNum1 * $height;
              $imageY = round(abs($imageHeight - $newImageHeight) / 2);
            }
          }
          else if ($scale == 1)
          {
            if ($imageWidth <= $width && $imageHeight <= $height)
            {
              $width = $imageWidth;
              $height = $imageHeight;
            }
            else
            {
              $scNum1 = $imageWidth / $width;
              $scNum2 = $imageHeight / $height;
              if ($imageWidth <= $width) $width = $imageWidth / $scNum2;
              else if ($imageHeight <= $height) $height = $imageHeight / $scNum1;
              else
              {
                if ($scNum1 >= $scNum2) $height = $imageHeight / $scNum1;
                else $width = $imageWidth / $scNum2;
              }
            }
          }
          $imgs = imagecreatetruecolor($width, $height);
          $bgColor = imagecolorallocate($imgs, 255, 255, 255);
          imagefill($imgs, 0, 0, $bgColor);
          imagecopyresampled($imgs, $img, 0, 0, $imageX, $imageY, $width, $height, $newImageWidth, $newImageHeight);
          if ($rotate != 0) $imgs = imagerotate($imgs, $rotate, $bgColor);
          if ($imageType == 'jpg' || $imageType == 'jpeg') $bool = imagejpeg($imgs, $URL2, $quality);
          else if ($imageType == 'gif') $bool = imagegif($imgs, $URL2);
          else if ($imageType == 'png') $bool = imagepng($imgs, $URL2);
          imagedestroy($img);
          imagedestroy($imgs);
        }
      }
      return $bool;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>