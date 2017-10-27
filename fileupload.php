<?phprequire_once("classes/images.php");
 require_once('includes/global.php'); 
 require_once("classes/clicks.php");
 $Pid= escString(decode($_GET['Id']));
 
  //banner upload
   $errmsg='';
  $image_path = IMAGE_PATH.'campaign/';
  if(isset($_POST['uploadSubmit']))
  {
   if($_FILES['image']['name'] != "")
  {
   // dimension checking
    $sizeArr   = getImageSize($_FILES['image']['tmp_name']);
   $maxWidth  = $sizeArr[0];
   $maxHeight = $sizeArr[1];
   if($maxWidth>='330' || $maxHeight>='50')
   {
     $errmsg ="Dont upload the new ad contain more than ( 320x40 ) dimensions.";
    }
   
   // file checking
   if($errmsg=='')
     $errmsg = checkForSingleFiles($_FILES, $imageTypes, $image_path);
   
     if($errmsg == "")
   {
   // setting Index
    $selectIndex = SelectImages('ai_index', "and adc_id='0' and a_id ='".$_SESSION['userId']."' order by  ai_id desc");
     
    if(sql_num_rows($selectIndex)!=0)
    {
     $fetchValueIndex = sql_fetch_array($selectIndex);
        $index=$fetchValueIndex['ai_index']+1;
    }
    else
    {
     $index=1;
     }
    
    $insId = InsertImages(" ai_index ='".$index."', ai_name ='".$_POST['bannerTitle']."',a_id ='".$_SESSION['userId']."'");
     // click for insert
    InsertClicks(" ai_id='".$insId."',pr_id='".$Pid."' ");
     
    // upload image
    $type  = uploadSingleFile($_FILES, $insId, $image_path);
     if($type != '0' && $type != '1')
    {
     UpdateImages('ai_url="'.$type.'"','ai_id="'.$insId.'"');
     header("location:index.php?page=imageUpload&Id='".$_GET['Id']."'&msg=1");
     }
    else if($type==1)
     $errmsg = "File type is not supported";
    else if($type==0)
     $errmsg = "There is a problem in saving";
   }
  }
  else
   $errmsg = "Select the image to upload";
   }
  
 //delete the banner
  if(isset($_GET['delId']))
  {
    $delId = escString(decode($_GET['delId']));
   $imageSelect = SelectImages('ai_url', ' and ai_id="'.$delId.'"');
    $FetchSelect = sql_fetch_array($imageSelect);
   $path=$image_path.$delId.'.'.$FetchSelect['ai_url'];
   deleteFile($path);
   DeleteImages('ai_id="'.$delId.'"');
  header("location:index.php?page=imageUpload&msg=2");
   }
?> 
<link rel="STYLESHEET" type="text/css" href="webresources/css/styles.css">
<script src="webresources/js/common.js" type="text/javascript"></script>
 <script  type="text/javascript" language="JavaScript">
function campignValidation()
{
 var formString=document.forms.imageUploadForm;
 if(formString.bannerTitle.value=="" )
  {
  alert ("Enter the Banner title");
  formString.bannerTitle.focus();
  return false;
 }
 if(formString.image.value=="")
 {
  alert ("Upload new ad");
  formString.image.focus();
   return false;
 }
}
</script>
<div id="TextIndent">
<form action="" method="post" name="imageUploadForm" enctype="multipart/form-data" onsubmit="return campignValidation();">
 <div><br>Choose existing ad banners from your image basket. If multiple banners are selected, they will rotate evenly:</div>
<div class="topspace15"></div>

<?php if($_GET['edit']=='1')
   {
     $selectImageEdit = FetchImages("ai_id", "and adc_id ='".$_SESSION['adcampId']."' and  a_id='".$_SESSION['userId']."' order by ai_id desc");
    $countArrayEdit  = count($selectImageEdit); 
   }
    $selectImage = FetchImages("*", " and a_id='".$_SESSION['userId']."' order by ai_id desc "); 
    $countArray  = count($selectImage); 
     $totalWidth = 327* $countArray;
     if($selectImage!=0) {    ?>
  <div class="multiselectbox" style="width:380px;height:100px;">
<div style="width:<?php echo $totalWidth; ?>px;padding:5px;">
  <?php if(isset($_GET['msg']) && ($_GET['msg']=='2'))  { ?>
<div style="color:green; text-align:left;">Your banner deleted successfully!</div>
 <?php   }  for($arr=0;$arr<$countArray;$arr++) {  
     $imagedisplay = IMAGE_PATH."campaign/".$selectImage[$arr]['ai_id'].'.'.$selectImage[$arr]['ai_url']; ?>
  <div class="fleft">
   <div><input type="checkbox" name="imageSelect"<?php if($selectImageEdit!=0) { 
     for($arr1=0;$arr1<$countArrayEdit;$arr1++) {  
     if($selectImage[$arr]['ai_id']==$selectImageEdit[$arr1]['ai_id'])  { 
      echo 'checked';   }   }  }   ?>>&nbsp;<span class="subtitleblack"><?php echo $selectImage[$arr]['ai_name']; ?></span>&nbsp;&nbsp;<a href="index.php?page=imageUpload&delId=<?php echo encode($selectImage[$arr]['ai_id']); ?>" title="delete" onclick="javascript:return confirm('Are you sure you want to Delete?')" class="footlink" >delete</a></div>
    <div class="topspace5"><img src="<?php echo $imagedisplay; ?>" width="319" height="40" alt="" style="float:left;postion:absolute;"></div>
   </div>
  <?php } ?>
 </div>
</div>
<?php } ?>
<div class="topspace15"></div>
<div style="text-align:left;"><a href="#nogo" title="Upload new ads..." onclick="return Toggle('reviewForm','imageArrow');">
 <img src="<?php echo IMAGE_PATH;?>arrowTop.gif"  alt="" id="imageArrow">&nbsp;Upload new ads...</a></div>
</div>
<?php if(isset($_GET['msg']) && ($_GET['msg']=='1') && $errmsg=='')  { ?>
 <div style="color:green; text-align:center;">Your banner uploaded successfully!</div>
<?php } if($errmsg!='') {  ?>
<div  style="color:red; text-align:center;"><?php echo $errmsg; ?></div>
 <?php } ?>
<div class="ReviewForm" id="reviewForm" style="display:block;">
<label>Banner title:</label>
<span style="text-align:left;padding-left:8px;width:170px;"><input type="text" name="bannerTitle" value=""></span>
 <label>Upload new ad:<br>(Ad size 320x40)</label>
<span><input type="File" name="image" value=""><br><input type="Submit" value="Upload" name="uploadSubmit"></span>
 <div class="clear"></div>
</div>
<div class="display"></form></div>
</div>
