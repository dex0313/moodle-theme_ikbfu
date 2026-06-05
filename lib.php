<?php

/*
* @package   theme_ikbfu
* @copyright 2021 Gleb Lobanov
* @copyright 2022 Dmitry Kharchuk
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

function theme_ikbfu_get_main_scss_content($theme) {                                                                                
    global $CFG;                                                                                                                    
                                                                                                                                    
    $scss = '';                                                                                                                     
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;                                                 
    $fs = get_file_storage();                                                                                                       
                                                                                                                                    
    $context = context_system::instance();                                                                                          
    if ($filename == 'default.scss') {                                                                                              
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.                      
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');                                        
    } else if ($filename == 'plain.scss') {                                                                                         
        // We still load the default preset files directly from the boost theme. No sense in duplicating them.                      
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');                                          
                                                                                                                                    
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_ikbfu', 'preset', 0, '/', $filename))) {              
        // This preset file was fetched from the file area for theme_ikbfu and not theme_boost (see the line above).                
        $scss .= $presetfile->get_content();                                                                                        
    } else {                                                                                                                        
        // Safety fallback - maybe new installs etc.                                                                                
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');                                        
    }                                                                                                                                       
    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.                                        
    $pre = file_get_contents($CFG->dirroot . '/theme/ikbfu/scss/pre.scss');                                                         
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.                                    
    $post = file_get_contents($CFG->dirroot . '/theme/ikbfu/scss/post.scss');                                                       

    // Combine them together.
    return $pre . "\n" . $scss . "\n" . $post;
    
}

function theme_ikbfu_get_extra_scss($theme) {
    $content = '';
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $content .= '@media (min-width: 768px) {';
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-size: cover;";
        $content .= ' } }';
    }

    // Sets the login background image.
    $loginbackgroundimageurl = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    $backgroundposition = '';
    $isdefaultloginimage = empty($loginbackgroundimageurl);
    if ($isdefaultloginimage) {
        // Use the default login background image.
        $loginbackgroundimageurl = $theme->image_url(
            'login_background',
            'theme',
        );
        // Set the default background position to center.
        $backgroundposition = 'background-position: center;';
    }
    $content .= 'body.pagelayout-login #page .login-layout-left { ';
    $content .= "background-image: url('$loginbackgroundimageurl'); ";
    $content .= "background-size: cover; {$backgroundposition} position: relative;";
    $content .= ' }';

    // Add a watermark to indicate the image is AI generated, but only for the default image.
    if ($isdefaultloginimage) {
        $content .= 'body.pagelayout-login #page .login-layout-left::after {';
        // Escape the label for use in a CSS string value: collapse newlines (which would break the CSS string)
        // and escape single quotes and backslashes via addcslashes.
        $ailabel = preg_replace('/[\r\n]+/', ' ', get_string('aigeneratedimage', 'theme_ikbfu'));
        $content .= " content: '" . addcslashes($ailabel, "'\\") . "';";
        $content .= ' position: absolute; bottom: 1rem; right: 1rem;';
        $content .= ' color: $white;';
        $content .= ' font-size: 0.8rem;';
        $content .= ' text-shadow: 0 1px 2px $black;';
        $content .= ' pointer-events: none;';
        $content .= ' }';
    }

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? "{$theme->settings->scss}  \n  {$content}" : $content;
}

// function theme_ikbfu2021_get_main_scss_content($theme) {
//     global $CFG;

//     $scss = '';
//     $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
//     $fs = get_file_storage();

//     $context = context_system::instance();
//     if ($filename == 'default.scss') {
//         // We still load the default preset files directly from the boost theme. No sense in duplicating them.
//         $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
//     } else if ($filename == 'plain.scss') {
//         // We still load the default preset files directly from the boost theme. No sense in duplicating them.
//         $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');

//     } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_ikbfu2021', 'preset', 0, '/', $filename))) {
//         // This preset file was fetched from the file area for theme_ikbfu2021 and not theme_boost (see the line above).
//         $scss .= $presetfile->get_content();
//     } else {
//         // Safety fallback - maybe new installs etc.
//         $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
//     }

//     // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.                                        
//     $pre = file_get_contents($CFG->dirroot . '/theme/ikbfu2021/scss/pre.scss');                                                         
//     // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.                                    
//     $post = file_get_contents($CFG->dirroot . '/theme/ikbfu2021/scss/post.scss'); 
    
//     return $pre . "\n" .  $scss . "\n" . $post;
// }

// function theme_ikbfu2021_update_settings_images($settingname) {                                                                         
//     global $CFG;                                                                                                                    
 
//     // The setting name that was updated comes as a string like 's_theme_ikbfu21_loginbackgroundimage'.                               
//     // We split it on '_' characters.                                                                                               
//     $parts = explode('_', $settingname);                                                                                            
//     // And get the last one to get the setting name..                                                                               
//     $settingname = end($parts);                                                                                                     
 
//     // Admin settings are stored in system context.                                                                                 
//     $syscontext = context_system::instance();                                                                                       
//     // This is the component name the setting is stored in.                                                                         
//     $component = 'theme_ikbfu2021';                                                                                                     
 
//     // This is the value of the admin setting which is the filename of the uploaded file.                                           
//     $filename = get_config($component, $settingname);                                                                               
//     // We extract the file extension because we want to preserve it.                                                                
//     $extension = substr($filename, strrpos($filename, '.') + 1);                                                                    
 
//     // This is the path in the moodle internal file system.                                                                         
//     $fullpath = "/{$syscontext->id}/{$component}/{$settingname}/0{$filename}";                                                      
//     // Get an instance of the moodle file storage.                                                                                  
//     $fs = get_file_storage();                                                                                                       
//     // This is an efficient way to get a file if we know the exact path.                                                            
//     if ($file = $fs->get_file_by_hash(sha1($fullpath))) {                                                                           
//         // We got the stored file - copy it to dataroot.                                                                            
//         // This location matches the searched for location in theme_config::resolve_image_location.                                 
//         $pathname = $CFG->dataroot . '/pix_plugins/theme/ikbfu2021/' . $settingname . '.' . $extension;                                 
 
//         // This pattern matches any previous files with maybe different file extensions.                                            
//         $pathpattern = $CFG->dataroot . '/pix_plugins/theme/ikbfu2021/' . $settingname . '.*';                                          
 
//         // Make sure this dir exists.                                                                                               
//         @mkdir($CFG->dataroot . '/pix_plugins/theme/ikbfu2021/', $CFG->directorypermissions, true);                                      
 
//         // Delete any existing files for this setting.                                                                              
//         foreach (glob($pathpattern) as $filename) {                                                                                 
//             @unlink($filename);                                                                                                     
//         }                                                                                                                           
 
//         // Copy the current file to this location.                                                                                  
//         $file->copy_content_to($pathname);                                                                                          
//     }                                                                                                                               
 
//     // Reset theme caches.                                                                                                          
//     theme_reset_all_caches();                                                                                                       
// }
