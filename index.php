<!DOCTYPE html>
<html>
<head>
<title>Image Segmentation</title>    
<script type="text/javascript">
var c;
var ctx;
var target;
var img_width;
var img_height;
var group_array;
var sentivity=200;
window.addEventListener('load', function () {
              
              function init(){
                  c=document.getElementById("myCanvas");
                  ctx=c.getContext("2d");
                  
                  var img = new Image();
                  img.src = 'image.jpg';
                  img.onload = function(){
                    ctx.drawImage(img, 1, 1);
                    img_width = img.width;
                    img_height = img.height;                    
                    target = ctx.getImageData(1,1,img_width,img_height);
                                        
                  }
                 
              }
              init();
});

function calculate_pos(x,y){
  return y*img_width*4 + x*4;
}

function find_diff(x,y,x1,y1){
    var pos1 = calculate_pos(x,y);
    var pos2 = calculate_pos(x1,y1);
    var sum = 0;
    for(var i = 0; i < 4; i++){
        sum += Math.abs(target.data[pos1+i]-target.data[pos2+i]);
    }
    return sum;
}

function plot_group(x,y, group){
  var left_x = x - 1;
  var right_x = x + 1;
  var up_y = y - 1;
  var down_y = y + 1;
  pos =calculate_pos(x,y);
  if(group_array[pos] != -1){
     return;      
  }else{
      group_array[pos+0] = group;
      group_array[pos+1] = group;
      group_array[pos+2] = group;
      group_array[pos+3] = group;
  }
  var total_diff;
  if(left_x >= 0){
      if(up_y >= 0){
          total_diff = find_diff(x,y,left_x,up_y);
          if(total_diff < sentivity){
              plot_group(left_x,up_y,group);
          }
      }
  
      total_diff = find_diff(x,y,left_x,y);
      if(total_diff < sentivity){
         plot_group(left_x,y,group);
      }
      if(down_y <= img_height){
        total_diff = find_diff(x,y,left_x,down_y);
        if(total_diff < sentivity){
           plot_group(left_x,down_y,group);
        }
      }
    
  }
  
   
      if(up_y >= 0){
          total_diff = find_diff(x,y,x,up_y);
          if(total_diff < sentivity){
              plot_group(x,up_y,group);
          }
      }
  
    
      if(down_y <= img_height){
        total_diff = find_diff(x,y,x,down_y);
        if(total_diff < sentivity){
           plot_group(x,down_y,group);
        }
      }
    
   if(right_x >= 0){
      if(up_y >= 0){
          total_diff = find_diff(x,y,right_x,up_y);
          if(total_diff < sentivity){
              plot_group(right_x,up_y,group);
          }
      }
  
      total_diff = find_diff(x,y,right_x,y);
      if(total_diff < sentivity){
         plot_group(right_x,y,group);
      }
      if(down_y <= img_height){
        total_diff = find_diff(x,y,right_x,down_y);
        if(total_diff < sentivity){
           plot_group(right_x,down_y,group);
        }
      }
    
  }
  
}

function segment(){
   img_size = target.data.length;
   group_array = new Array(img_size);
   var seg_img = new Uint8ClampedArray(img_size);
   var group = 0;
   for(var i = 0; i < group_array.length; i++){
       group_array[i] = -1;
   }
   for(var y = 0; y < img_height; y++){
       for(var x = 0; x < img_width; x++){
           pos = calculate_pos(x,y);
           if(group_array[pos] == -1){
              plot_group(x,y,group);
              group++;    
            
           }
           
       }
   }
   alert("num of groups" + group);
   for(var y = 0; y < img_height; y++){
       for(var x = 0; x < img_width; x++){
          seg_img = transfer_pixel(seg_img,x,y);
       }
   }
   show(seg_img);
}

function edge_segment(x,y){
   var pos = calculate_pos(x,y);
   if((x == 0) ||(y==0)||(x==img_width-1)||(y==img_height-1)){ return true;}
    if((x-1) >= 0){
        if((y-1) >= 0){
            var pos1 = calculate_pos(x-1,y-1);
            if(group_array[pos] != group_array[pos1]){
                return true;                
            }
        }
         var pos2 = calculate_pos(x-1,y);
            if(group_array[pos] != group_array[pos2]){
                return true;                
            }
         if((y+1) <= img_height){
            var pos3 = calculate_pos(x-1,y+1);
            if(group_array[pos] != group_array[pos3]){
                return true;                
            }
        }
    }
    if((y-1) >= 0){
            var pos4 = calculate_pos(x,y-1);
            if(group_array[pos] != group_array[pos4]){
                return true;                
            }
     }
     if((y+1) <= img_height){
            var pos5 = calculate_pos(x,y+1);
            if(group_array[pos] != group_array[pos5]){
                return true;                
            }
        }
    if((x+1) >= 0){
        if((y-1) >= 0){
            var pos6 = calculate_pos(x+1,y-1);
            if(group_array[pos] != group_array[pos6]){
                return true;                
            }
        }
         var pos7 = calculate_pos(x+1,y);
            if(group_array[pos] != group_array[pos7]){
                return true;                
            }
         if((y+1) <= img_height){
            var pos8 = calculate_pos(x+1,y+1);
            if(group_array[pos] != group_array[pos8]){
                return true;                
            }
        }
    }    
    return false;
}

function transfer_pixel(seg_img, x,y){
    var pos = calculate_pos(x,y);
    
    if(edge_segment(x,y)){
       seg_img[pos+0] = 128;
       seg_img[pos+1] = 0;
       seg_img[pos+2] = 0;
       seg_img[pos+3] = 255;
    }else{
       seg_img[pos+0] = target.data[pos+0];
       seg_img[pos+1] = target.data[pos+1];
       seg_img[pos+2] = target.data[pos+2];
       seg_img[pos+3] = target.data[pos+3];
    }
    return seg_img;    
}   

function show(seg_img)
{        
    var imgData=ctx.getImageData(1,1,img_width,img_height);
      
      for(var i = 0; i < imgData.data.length; i++ ){
        imgData.data[i] = seg_img[i];        
      }    
      ctx.putImageData(imgData,1,img_height+40);    
}
</script>
</head>
    
<body>

<canvas id="myCanvas" width="900" height="250" style="border:1px solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.</canvas>

<button onclick="segment()">Segment</button>

</body>
</html>
