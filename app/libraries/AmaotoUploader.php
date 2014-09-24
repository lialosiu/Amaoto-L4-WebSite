<?php

/**
 * Class AmaotoUploader
 */
class AmaotoUploader
{
    /**
     * @param string $path
     * @return string
     */
    public static function getUploadToPath($path = 'upload')
    {
        $path = public_path($path);
        if (!file_exists($path))
            mkdir($path);
        $path = $path . '/' . date("Y");
        if (!file_exists($path))
            mkdir($path);
        $path = $path . '/' . date("m");
        if (!file_exists($path))
            mkdir($path);
        $path = $path . '/' . date("d");
        if (!file_exists($path))
            mkdir($path);
        return $path;
    }

    /**
     * @param $userId
     * @param $fileUniName
     * @param $oriFileName
     * @param $tmpFileUrl
     * @param $chunk
     * @param $chunks
     * @param string $uploadTo
     * @return AmaotoFile|\Illuminate\Database\Eloquent\Model|mixed|null|static
     * @throws NeedMoreDataException
     */
    public static function uploadFile($userId, $fileUniName, $oriFileName, $tmpFileUrl, $chunk, $chunks, $uploadTo = 'upload')
    {
        $fileMergePath = self::getUploadToPath($uploadTo) . '/' . $fileUniName . '.merge';

        if ($chunk === 0)
            File::put($fileMergePath, File::get($tmpFileUrl));
        else
            File::append($fileMergePath, File::get($tmpFileUrl));

        if (!$chunks || $chunk == $chunks - 1) {
            //文件已上传完整

            //计算哈希值
            $fileMd5      = md5_file($fileMergePath);
            $fileFinalUrl = self::getUploadToPath($uploadTo) . '/' . $fileMd5 . '.' . File::extension($oriFileName);

            //判断文件是否存在
            if (file_exists($fileFinalUrl)) {
                File::delete($fileMergePath);
            } else {
                File::move($fileMergePath, $fileFinalUrl);
            }

            if (AmaotoFile::whereMd5($fileMd5)->count() == 0) {
                $thatFile          = new AmaotoFile;
                $thatFile->md5     = $fileMd5;
                $thatFile->name    = $oriFileName;
                $thatFile->size    = File::size($fileFinalUrl);
                $thatFile->url     = str_replace(public_path(), '', $fileFinalUrl);
                $thatFile->user_id = $userId;
                $thatFile->updateTypeByGetId3();
                $thatFile->save();
                return $thatFile;
            } else {
                $thatFile = AmaotoFile::whereMd5($fileMd5)->first();
                return $thatFile;
            }
        }

        throw new NeedMoreDataException('文件未接收完整，请求继续发送数据');
    }

}