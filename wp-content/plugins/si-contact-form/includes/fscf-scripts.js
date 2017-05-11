/*
 * fscf-scripts.js
 * Script file for Fast Secure Contact Form
 * Created by Mike Challis and Ken Carlson
 */


function fscf_captcha_refresh(form_num,securimage_url,securimage_show_url) {
   var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
   var string_length = 16;
   var prefix = '';
   for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		prefix += chars.substring(rnum,rnum+1);
   }
  document.getElementById('fscf_captcha_prefix' + form_num).value = prefix;

  var si_image_ctf = securimage_show_url + prefix;
  document.getElementById('fscf_captcha_image' + form_num).src = si_image_ctf;

}
