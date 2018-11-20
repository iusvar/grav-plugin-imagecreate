<?php
namespace Grav\Plugin\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class ImagecreateShortcode extends Shortcode
{
    protected $fontname;

    public function init()
    {
        $this->shortcode->getHandlers()->add('ic', array($this, 'process'));
    }

    public function process(ShortcodeInterface $sc)
    {
        $fn = $sc->getParameter('file', null);
        if ($fn === null) {
            $fn = $sc->getShortcodeText();
            $fn = str_replace('[ic=', '', $fn);
            $fn = str_replace('/]', '', $fn);
            $fn = str_replace(']', '', $fn);
            $fn = trim($fn);
        }
        if ( ($fn === null) && ($fn === '') ) {
            return "<p>Image Create: Malformed shortcode (<tt>".htmlspecialchars($sc->getShortcodeText())."</tt>).</p>";
        }

        // Gets the parameters entered on the markdown page
        $string     = $sc->getParameter('string', null);
        $fontsize   = $sc->getParameter('fontsize', null);
        
        $falpha = $sc->getParameter('falpha', null);
        $falpha = 127 - ( $falpha * 127 );
        $fore_color = $sc->getParameter('fcolor', null);
        $fcolor = $this->hex2rgba($fore_color, true);
        $afcolor = sscanf($fcolor, "rgba(%d, %d, %d, %f)");

        $balpha = $sc->getParameter('balpha', null);
        $balpha = 127 - ( $balpha * 127 );
        $back_color = $sc->getParameter('bcolor', null);
        $bcolor = $this->hex2rgba($back_color, true);
        $abcolor = sscanf($bcolor, "rgba(%d, %d, %d, %f)");
        
        $width      = $sc->getParameter('width', null);
        $height     = $sc->getParameter('height', null);
        $image_type = $sc->getParameter('image_type', null);
        $quality    = $sc->getParameter('quality', null);
        $position   = $sc->getParameter('position', null);

        $demo = $sc->getParameter('demo', null);
        if ($demo) {
			$shortcode = $sc->getShortcodeText();
			$shortcode = str_replace(' demo="true"','',$shortcode);
			return "<tt>".htmlspecialchars($shortcode)."</tt>";
		}

        // Check if the variables contain useful values otherwise
        // they are taken from the plugin configuration file
        if ($string === null) $string = $this->config->get('plugins.imagecreate.parameters.string');
        if ($width === null) $width = $this->config->get('plugins.imagecreate.parameters.width');
        if ($height === null) $height = $this->config->get('plugins.imagecreate.parameters.height');
        if ($position === null) $position = $this->config->get('plugins.imagecreate.parameters.position');
        if ($fontsize === null) $fontsize = $this->config->get('plugins.imagecreate.parameters.fontsize');
        if ($image_type == null) $image_type = $this->config->get('plugins.imagecreate.parameters.image_type');

        if ($fore_color === null) {
            $fcolor = $this->config->get('plugins.imagecreate.parameters.foreground_color');
            $afcolor = sscanf($fcolor, "rgba(%d, %d, %d, %f)");
            $falpha = 127 - ( $afcolor[3] * 127 );
            
        }
        if ($back_color === null) {
            $bcolor = $this->config->get('plugins.imagecreate.parameters.background_color');
            $abcolor = sscanf($bcolor, "rgba(%d, %d, %d, %f)");
            $balpha = 127 - ( $abcolor[3] * 127 );
        }
        if ($quality === null) $quality = $this->config->get('plugins.imagecreate.parameters.quality');

        $output = '';

        $font = $this->config->get('plugins.imagecreate.truetype');
        
        // Set the enviroment variable for GD
        $path = realpath( dirname(__FILE__) .'/fonts' );
        putenv( 'GDFONTPATH='. $path );
        
        // Name the font to be used
        $this->fontname = getenv('GDFONTPATH') .DS. $font;

        // Calculates and returns the bounding box in pixels for a FreeType text.
        $box = imageftbbox( $fontsize, 0, $this->fontname, $string );
        $w = abs($box[0])+abs($box[2])+10;
        $h = abs($box[1])+abs($box[7])+6;

        // Create a new palette based image
        $image = imagecreate( $w, $h );

        // Allocate a color for an image
        $text_color = imagecolorallocatealpha( $image, $afcolor[0], $afcolor[1], $afcolor[2], $falpha );
        $background = imagecolorallocatealpha( $image, $abcolor[0], $abcolor[1], $abcolor[2], $balpha );

        // Draw a filled rectangle
        imagefilledrectangle( $image, 0, 0, $w, $h, $background );

        // Calculates the points where you place text to center it
        // horizontally and vertically into an image.
        $x = ($w - ($box[2] - $box[0])) / 2; 
        $y = ($h - ($box[1] - $box[7])) / 2;
        $y -= $box[7];

        $angle = 0;

        // Write text to the image using TrueType fonts
        imagettftext($image, $fontsize, $angle, $x, $y, $text_color, $this->fontname, $string);

        /*
         * start a new output buffer
         * generate the image
         * grab the buffer contents
         * stop this output buffer
         */
        ob_start();
        switch ($image_type) {
            case 'gif':
                imagegif( $image, NULL);
                break;
            case 'jpeg':
                imagejpeg( $image, NULL, $quality );
                break;
            case 'png':
                imagepng( $image, NULL, $quality );
                break;
        }
        $contents = ob_get_contents();
        ob_end_clean();

        // De-allocate a color for an image
        imagecolordeallocate($image, $background );
        imagecolordeallocate($image, $text_color );

        // frees any memory associated with image
        imagedestroy($image);

        // Image Position: centered, left, right
        $style = "style='STYLE'";
        switch ($position) {
            case 'centered':
                $style = str_replace('STYLE','display: block;margin-left: auto;margin-right: auto;',$style);
                break;
            case 'left':
                $style = str_replace('STYLE','display: block;margin-right: auto;',$style);
                break;
            case 'right':
                $style = str_replace('STYLE','display: block;margin-left: auto;',$style);
                break;
        }

        $style= "style='vertical-align:middle;'";
        // Returns the output to display
        $output = "<img src='data:image/".$image_type.";base64,".base64_encode($contents)."' ".$style."/>";
        return $output;
    }


    /* Convert hexdec color string to rgb(a) string */
	public function hex2rgba($color, $opacity = false)
	{
		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
		return $default; 

		//Sanitize $color if "#" is provided 
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		//Check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}

		//Return rgb(a) color string
		return $output;
	}

}
