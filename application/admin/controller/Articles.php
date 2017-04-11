<?php
namespace app\admin\controller;
use app\admin\model\Article;
use think\Loader;

class Articles extends Base
{
    /**
     * 文章详情展示
     * @return mixed
     */
    public function index()
    {

        //得到总记录数计算出总页数
        $total = Article::GetTotal();
        $pageCount = ceil($total/10);
        $page = isset($_GET['page'])?$_GET['page']:'1';
        $result = Article::ShowArticlesByPage($page);

        $this->assign('page',$page);
        $this->assign('pageCount',$pageCount);
        $this->assign('result',$result);
        return $this->fetch('/article-list');
    }

    /**
     * 添加文章
     */
    public function addArticle()
    {
        //引入simple_html_dom操作类库
        Loader::import('simplehtmldom.simple_html_dom');
        $url = $_POST['url'];
        $type = $_POST['type'];
        $ch = curl_init();

        //过滤html代码提取纯文本
        function filter($str){
            $str = str_replace(' ','',$str);
            $result = strip_tags($str);
            return $result;
        }

        //下载缩略图
        function DownLoadthumbnail($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            $cp = getcwd();
            if(!is_dir($cp.'\thumbnails'))
            {
                mkdir($cp.'\thumbnails');
            }

            //获取当前时间戳防止文件重名
            date_default_timezone_set('PRC');
            $name = date('YmdHis');
            $fp = "$cp\\thumbnails\\$name.jpeg";
            $img = fopen($fp,"a");
            fwrite($img, $content);
            fclose($img);
            curl_close($ch);

            //调用压缩图片方法
            $stat = img2thumb($fp, $fp, $width = 600, $height = 400, $cut = 0, $proportion = 0.8);
            if(!$stat){
                echo 'Resize Image Fail!';
                exit;
            }

            //完成将文件名返回
            return "$name.jpeg";
        }

        //下载原图
        function DownLoadOriginal($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            $path = getcwd();

            if(!is_dir($path.'\originals'))
            {
                mkdir($path.'\originals');
            }

            //获取当前时间戳防止重名
            date_default_timezone_set('PRC');
            $name = date('YmdHis');
            $images = fopen("$path\\originals\\$name.jpeg","a");
            fwrite($images, $content);
            fclose($images);
            curl_close($ch);
            return "$name.jpeg";
        }

        //压缩图片
        function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
        {
            if(!is_file($src_img))
            {
                return false;
            }
            $ot = fileext($dst_img);
            $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
            $srcinfo = getimagesize($src_img);
            $src_w = $srcinfo[0];
            $src_h = $srcinfo[1];
            $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
            $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

            $dst_h = $height;
            $dst_w = $width;
            $x = $y = 0;

            /**
             * 缩略图不超过源图尺寸（前提是宽或高只有一个）
             */
            if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
            {
                $proportion = 1;
            }
            if($width> $src_w)
            {
                $dst_w = $width = $src_w;
            }
            if($height> $src_h)
            {
                $dst_h = $height = $src_h;
            }

            if(!$width && !$height && !$proportion)
            {
                return false;
            }
            if(!$proportion)
            {
                if($cut == 0)
                {
                    if($dst_w && $dst_h)
                    {
                        if($dst_w/$src_w> $dst_h/$src_h)
                        {
                            $dst_w = $src_w * ($dst_h / $src_h);
                            $x = 0 - ($dst_w - $width) / 2;
                        }
                        else
                        {
                            $dst_h = $src_h * ($dst_w / $src_w);
                            $y = 0 - ($dst_h - $height) / 2;
                        }
                    }
                    else if($dst_w xor $dst_h)
                    {
                        if($dst_w && !$dst_h)  //有宽无高
                        {
                            $propor = $dst_w / $src_w;
                            $height = $dst_h  = $src_h * $propor;
                        }
                        else if(!$dst_w && $dst_h)  //有高无宽
                        {
                            $propor = $dst_h / $src_h;
                            $width  = $dst_w = $src_w * $propor;
                        }
                    }
                }
                else
                {
                    if(!$dst_h)  //裁剪时无高
                    {
                        $height = $dst_h = $dst_w;
                    }
                    if(!$dst_w)  //裁剪时无宽
                    {
                        $width = $dst_w = $dst_h;
                    }
                    $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
                    $dst_w = (int)round($src_w * $propor);
                    $dst_h = (int)round($src_h * $propor);
                    $x = ($width - $dst_w) / 2;
                    $y = ($height - $dst_h) / 2;
                }
            }
            else
            {
                $proportion = min($proportion, 1);
                $height = $dst_h = $src_h * $proportion;
                $width  = $dst_w = $src_w * $proportion;
            }

            $src = $createfun($src_img);
            $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefill($dst, 0, 0, $white);

            if(function_exists('imagecopyresampled'))
            {
                imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
            }
            else
            {
                imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
            }
            $otfunc($dst, $dst_img);
            imagedestroy($dst);
            imagedestroy($src);
            return true;
        }

        function fileext($file)
        {
            return pathinfo($file, PATHINFO_EXTENSION);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //装载html
        $content = curl_exec($ch);

        //删除js
        $search ="'<script[^>]*?>.*?</script>'si";
        $replace = "";
        $content = preg_replace($search,$replace,$content);

        //删除head
        $search ="'<head[^>]*?>.*?</head>'si";
        $content = preg_replace($search,$replace,$content);

        //删除防盗链
        $content = str_replace('data-src','src',$content);

        //删除&nbsp;
        $content = str_replace('&nbsp;','',$content);

        //删除留言板块
        $a = (strpos($content,'div class="ct_mpda_wrp"'));
        $b = (strpos($content,'<div/>'));
        $remove = substr($content,$a-1,$b-5);
        $content = str_replace($remove,"</html>",$content);

        //实例化simple_html_dom类库
        $dom = str_get_html($content);
        //$dom = new simple_html_dom($content);

        $title = filter($dom->find('h2',0));
        $date = filter($dom->find('[id=post-date]',0));
        $sourceName = filter($dom->find('[id=post-user]',0));
        $sourceNum = filter($dom->find('[class=profile_meta_value]',0));

        //保存文章前3张图片作为预览页面的缩略图
        $i =-1;
        foreach($dom->find('img[data-type]') as $element){
            $i++;
            if($i<=2){
                $src = $element->src;
                sleep(1);
                $pathArr[$i]=DownLoadthumbnail($src);
            }
            else{
                break;
            }
        }

        $thumbnails = implode(';',$pathArr);//数组拼接成字符串

        //抓取到文章中所有的p标签，文字和图片都保存在其中
        $introduce = $dom->find('p');
        //定义2个数组
        $textArr = array(
            'p'=>array(),
            'img'=>array(),
            'width'=>array(),
            'height'=>array()
        );

        $imgArr = array();
        $widthArr = array();
        $heightArr = array();

        //保存图片并将图片名称保存入$imgArr
        $contentImg = $dom->find('img');
        $contentImg=array_splice($contentImg,1);

        foreach($contentImg as $element){
            $src = $element->src;
            sleep(1);
            //调用DownLoadImages函数，该函数将图片抓取到本地保存并返回被保存的文件名
            //将本地图片文件名追加入$imgArr数组中
            $name = DownLoadOriginal($src);
            $imgInfo = getimagesize("./originals/$name");
            $width = $imgInfo[0];
            $height = $imgInfo[1];
            array_push($imgArr,$name);
            array_push($widthArr,$width);
            array_push($heightArr,$height);
        }

        array_push($imgArr,"empty.jpg");

        //遍历$introduce装入二维数组$textArr
        foreach ($introduce as $value){

            //判断如果只有一个<br>标签就自动忽略
            if(!preg_match("/<br/",$value) && !filter($value)==null || preg_match("/<img/",$value)){

                $value = filter($value);
                if ($value==null){
                    array_push($textArr['p'],$value);
                    array_push($textArr['img'],"img.jpg");
                    array_push($textArr['width'],"");
                    array_push($textArr['height'],"");
                }else {
                    array_push($textArr['img'],"empty.jpg");
                    array_push($textArr['p'], $value);
                    array_push($textArr['width'],"");
                    array_push($textArr['height'],"");
                }
            }
        }

        $i=0;
        foreach ($textArr['img'] as $key=>$value){
            if($value == "img.jpg"){
                if($i == count($imgArr)){
                    break;
                }
                $textArr['img'][$key] = $imgArr[$i];
                $textArr['width'][$key] = $widthArr[$i];
                $textArr['height'][$key] = $heightArr[$i];
                $i++;
            }
        }
        //内容转为json格式以存入数据库
        $data = json_encode($textArr);
        $bool = Article::addArticle($title,$date,$sourceName,$sourceNum,$url,$thumbnails,$type,$data);
        $dom->clear();
        curl_close($ch);
        if($bool = true){
            return $this->success('添加成功','/admin/articles');
        }else{
            return $this->error('添加失败','/admin/articles');
        }
    }

    /**
     * 删除文章
     * @return bool 返回执行结果
     */
    public function delArticle(){
        $id  = isset($_GET['id']) ? $_GET['id'] : null;
        $info = Article::getArticleById($id)['0'];
        $delImgArr = explode(";",$info['thumbnails']);
        $jsonToArr = (json_decode($info['content']));
        $imgArr =array();
        foreach ($jsonToArr as$key=> $item){
            if ($key =="img"){
                foreach ($item as $value) {
                    if (!($value=="empty.jpg")){
                        array_push($imgArr,$value);
                    }
                }
            }
        }
        foreach ($delImgArr as $value){
            unlink("thumbnails/".$value);
        }
        foreach ($imgArr as $value){
            unlink("originals/".$value);
        }
        $bool = Article::delArticle($id);
        if($bool = true){
            return $this->success('删除成功','/admin/articles');
        }else{
            return $this->error('删除失败','/admin/articles');
        }
    }
}