<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class upload
  {
    public static function up2self($argFile, $argLimit = '', $argTargetPath = '')
    {
      $file = $argFile;
      $limit = $argLimit;
      $targetPath = $argTargetPath;
      $limitFileResizeAry = null;
      $upResultArray = array();
      $upResultArray['status'] = 0;
      $upResultArray['message'] = '';
      $upResultArray['para'] = '';
      $uploadPath = tpl::take('config.upload_path', 'cfg');
      $allowFiletype = tpl::take('config.upload_filetype', 'cfg');
      $allowFilesize = base::getNum(tpl::take('config.upload_filesize', 'cfg'), 0);
      if (base::isEmpty($allowFiletype)) $allowFiletype = tpl::take('global.config.upload_filetype', 'cfg');
      if ($allowFilesize == 0) $allowFilesize = base::getNum(tpl::take('global.config.upload_filesize', 'cfg'), 0);
      if (!base::isEmpty($limit))
      {
        $limitFiletype = tpl::take('config.upload_filetype_limit_' . $limit, 'cfg');
        $limitFilesize = base::getNum(tpl::take('config.upload_filesize_limit_' . $limit, 'cfg'), 0);
        $limitFileResize = tpl::take('config.upload_fileresize_limit_' . $limit, 'cfg');
        if (!base::isEmpty($limitFiletype)) $allowFiletype = $limitFiletype;
        if ($limitFilesize != 0) $allowFilesize = $limitFilesize;
        if (!base::isEmpty($limitFileResize)) $limitFileResizeAry = json_decode($limitFileResize, true);
      }
      if (is_array($file))
      {
        $filename = $file['name'];
        $tmp_filename = $file['tmp_name'];
        $filesize = base::getNum($file['size'], 0);
        $filetype = strtolower(base::getLRStr($filename, '.', 'right'));
        if (base::isEmpty($tmp_filename))
        {
          $upResultArray['message'] = tpl::take('::console.text-upload-error-1', 'lng');
        }
        else if (!base::checkInstr($allowFiletype, $filetype, ','))
        {
          $upResultArray['message'] = str_replace('{$allowfiletype}', $allowFiletype, tpl::take('::console.text-upload-error-2', 'lng'));
        }
        else if ($filesize > $allowFilesize)
        {
          $upResultArray['message'] = str_replace('{$allowfilesize}', base::formatFileSize($allowFilesize), tpl::take('::console.text-upload-error-3', 'lng'));
        }
        else
        {
          $canMove = false;
          if (!base::isEmpty($targetPath))
          {
            if ($filetype != base::getLRStr($targetPath, '.', 'right')) $upResultArray['message'] = str_replace('{$filetype}', $filetype, tpl::take('::console.text-upload-error-4', 'lng'));
            else
            {
              $canMove = true;
              $uploadFullPath = $targetPath;
            }
          }
          else
          {
            $canMove = true;
            $uploadPath = $uploadPath . base::formatDate(base::getDateTime(), '-1') . '/' . base::formatDate(base::getDateTime(), '-2') . base::formatDate(base::getDateTime(), '-3') . '/';
            $uploadFullPath = $uploadPath . base::formatDate(base::getDateTime(), '11') . base::getRandomString(2) . '.' . $filetype;
            if (!is_dir($uploadPath)) @mkdir($uploadPath, 0777, true);
          }
          if ($canMove == true)
          {
            if (move_uploaded_file($tmp_filename, $uploadFullPath))
            {
              if (base::isImage($filetype) && !empty($limitFileResizeAry))
              {
                $resizeWidth = base::getNum($limitFileResizeAry['width'], 0);
                $resizeHeight = base::getNum($limitFileResizeAry['height'], 0);
                $resizeScale = base::getNum($limitFileResizeAry['scale'], 0);
                $resizeQuality = base::getNum($limitFileResizeAry['quality'], 0);
                image::resizeImage($uploadFullPath, $uploadFullPath, $resizeWidth, $resizeHeight, $resizeScale, 0, $resizeQuality);
              }
              $paraArray = array();
              $paraArray['filename'] = $filename;
              $paraArray['filesize'] = $filesize;
              $paraArray['filetype'] = $filetype;
              $paraArray['filepath'] = $uploadFullPath;
              $paraArray['fileurl'] = $uploadFullPath;
              $paraArray['filesizetext'] = base::formatFileSize($filesize);
              $upResultArray['status'] = 1;
              $upResultArray['para'] = json_encode($paraArray);
            }
          }
        }
      }
      $tmpstr = json_encode($upResultArray);
      return $tmpstr;
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>
