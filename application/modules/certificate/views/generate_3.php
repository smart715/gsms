<!DOCTYPE html>
<html>
<head>      

        <title><?php echo $this->lang->line('generate_certificate'); ?></title>
        
        <?php if ($this->global_setting->favicon_icon) { ?>
            <link rel="icon" href="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $this->global_setting->favicon_icon; ?>" type="image/x-icon" />             
        <?php } else { ?>
            <link rel="icon" href="<?php echo IMG_URL; ?>favicon.ico" type="image/x-icon" />
        <?php } ?>    
        
         <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Allerta+Stencil" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans+Extra+Condensed" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Limelight" rel="stylesheet">  
        <link href="https://fonts.googleapis.com/css?family=Michroma" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Prosto+One" rel="stylesheet">         
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo CSS_URL; ?>front/bootstrap.min.css"> 
        <link rel="stylesheet" href="<?php echo CSS_URL; ?>front/fontawesome-all.min.css">
        <!-- Custom Theme Style -->
        <link href="<?php echo CSS_URL; ?>custom.css" rel="stylesheet">
        
        <style>
            body {background: #fff;}
            @page { margin: 0; }   
            @media print {
                .certificate {                   
                    background: url("<?php echo IMG_URL; ?>certificate/template3.png") no-repeat !important;    
                    min-height: 550px;
                    padding: 10%;
                    width: 100%;
                    margin-left: auto;
                    margin-right: auto;
                    background-size: 100% 100% !important;
                   -webkit-print-color-adjust: exact !important; 
                    color-adjust: exact !important; 
                    text-align: center;
                }
                .name-text {               
                    text-align: center !important;                   
                }  
            } 
   
            .certificate {
                width: 800px;
                min-height: 550px;
                margin-left: auto;
                margin-right: auto;
                padding: 80px 60px;
                background: url("<?php echo IMG_URL; ?>certificate/template3.png") no-repeat;    
                background-size: 100% 100%;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                text-align: center;
                overflow-wrap: anywhere;
            }
            
            .certificate-top {
                    position: relative;
            }

            .certificate-title-img{
                max-width: 50px;
                max-height: 50px;
            }
            
            .sub-title-img {
                font-family: 'Prosto One', cursive;   
                height: 100%;
                padding-top: 20px;
                text-align: center;
            }         
            .top-heading-title {
                text-align: center;
                font-family: 'Prosto One', cursive;
                margin: 20px 30px 10px 30px;  
                overflow-wrap: anywhere;
                color: #2c455e;
                font-size: 30px;
                font-weight: 600;
            } 
                        
            .name-text {
                font-family: cursive;
                font-size: 50px;   
                color: #2c455e;
                text-align: center !important;
                margin: 60px 0 0 0;

            }   
            .award-text{                
                font-family: inherit;
                font-size: 16px;
                color: #7e90a2;
                text-align: center !important;
                margin: 25px;
                font-weight: 600;
            }
            .name-section {
                text-align: center;
            }    
            .main-text-block {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }
            .main-text {
                font-family: none;
                font-size: 15px;
                line-height: 17px;
                letter-spacing: -1px;
                max-width: 250px;
                text-align: center;
            }              
            .main-text span{
                padding: 0px 20px 0px 20px;                    
                text-decoration: underline;
            }
            .footer-section {
                margin-top: 30px;
            }
            .footer_text {
                font-family: 'Prosto One', cursive;
                font-size: 14px;
                border-top: 1px dashed;
                text-align: center;
                padding-top: 10px;
                margin :0px  10px 0px 10px;
                width: 30%; 
            }
            .text-footer{    
                padding: 10px 70px;
            }
            .footer-label{                
                color: #7e90a2;
                font-size: 15px;
                font-weight: 600;
            }
            .signature-img{
                max-width: 200px;
                max-height: 80px;
            }


            

    </style>
    </head>

    <body>
        <div class="x_content">
             <div class="row">
                 <div class="col-sm-12">                 
                    <div class="certificate">

                        <div class="certificate-top">  
                            <span class="sub-title-img">
                                  <?php if ($school->logo) { ?>
                                    <img class="certificate-title-img" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $school->logo; ?>" alt="" /> 
                                 <?php } elseif ($school->frontend_logo) { ?>
                                    <img class="certificate-title-img" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $school->frontend_logo; ?>" alt="" /> 
                                 <?php } else { ?>                                                        
                                    <img class="certificate-title-img" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $this->global_setting->brand_logo; ?>" alt=""  />
                                 <?php } ?>
                            </span>                     
                            <h2 class="top-heading-title"><?php echo $certificate->top_title; ?></h2>                              
                         
                        </div>
                       <div class="clear"></div>
                        <div class="name-section">    
                                           
                            <div style="text-align:center;">
                                <h3 class="award-text">PRESENTED TO</h3>
                            </div>                        
                            <div style="text-align:center;">
                                <h3 class="name-text"><?php echo $certificate->student_name; ?></h3>
                            </div>    
                            
                            <hr style="height:3px;border-width:0;color:gray;background-color:#627485;margin: 0px 100px 0px 100px;">                       
                        </div>
                        <div class="clear"></div>
                        <div class="main-text-block">
                            <p class="main-text">
                                <?php echo $certificate->main_text; ?>
                            </p>
                        </div>
                        <!-- <div class="footer-section">
                            <div class="row" >
                                <div class="">
                                  <?php if ($certificate->background) { ?>
                                    <img class="left-signature" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $certificate->background; ?>" alt="" /> 
                                 <?php } ?>
                                </div>
                                <div class="">
                                  <?php if ($certificate->background) { ?>
                                    <img class="right-signature" src="<?php echo UPLOAD_PATH; ?>/logo/<?php echo $certificate->background; ?>" alt="" /> 
                                 <?php } ?>
                                </div>
                            </div>
                        </div> -->
                        <div class="footer-section">
                            <div class="row" >
                                <div class="col-sm-6 text-footer">
                                    <div style="height:80px;">
                                    <?php if ($certificate->sign1) { ?>
                                        <img class="signature-img" src="<?php echo UPLOAD_PATH; ?>/certificate/sign/<?php echo $certificate->sign1; ?>" alt="" /> 
                                    <?php } ?>
                                    </div>
                                    <hr style="height:3px;border-width:0;color:gray;background-color:#000;margin: 0px 10px 10px 10px;">
                                    <span class="footer-label"><?php echo $certificate->signer_name1; ?></span><br>
                                    <span class="footer-label"><?php echo $certificate->footer_left; ?></span>
                                </div>
                                <div class="col-sm-6 text-footer">
                                    <div style="height:80px;">
                                    <?php if ($certificate->sign2) { ?>
                                        <img class="signature-img" src="<?php echo UPLOAD_PATH; ?>/certificate/sign/<?php echo $certificate->sign2; ?>" alt="" /> 
                                    <?php } ?>
                                    </div>
                                    <hr style="height:3px;border-width:0;color:gray;background-color:#000;margin: 0px 10px 10px 10px;">
                                    <span class="footer-label"><?php echo $certificate->signer_name2; ?></span><br>
                                    <span class="footer-label"><?php echo $certificate->footer_right; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>                 
                 </div>
             </div>

            <!-- this row will not appear when printing -->
            <div style="display: flex; justify-content: center;margin: 30px;">
            <div class="row no-print">
                <div class="col-xs-12">
                    <button class="btn btn btn-primary btn-lg" onclick="window.print();" style="width:150px;"><i class="fa fa-print"></i> <?php echo $this->lang->line('print'); ?></button>
                </div>
            </div>
            </div>
        </div>
    </body>
</html>