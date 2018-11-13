# Imagecreate Plugin

The ![](images/ic1.png) Plugin is for [Grav CMS](http://github.com/getgrav/grav). Create a new image with a text using TrueType fonts.

## Installation

Installing the Imagecreate plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install imagecreate

This will install the Imagecreate plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/imagecreate`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `imagecreate`. You can find these files on [GitHub](https://github.com/severo-iuliano/grav-plugin-imagecreate) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/imagecreate

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/imagecreate/imagecreate.yaml` to `user/config/plugins/imagecreate.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
string: email@example.com
fontsize: 10
foreground_color: 'rgba(156, 29, 61, 1)'
background_color: 'rgba(249, 242, 244, 1)'
width: 210
height: 27
image_type: png
quality: 6
position: centered
```

## Usage

This plugin creates a new image with a text using TrueType fonts.

### Creating an Image

This plugin uses the [Shortcode Core](https://github.com/getgrav/grav-plugin-shortcode-core) infrastructure. Read those docs for the nitty gritty of how shortcodes work.

The `Imagecreate` shortcode is a self-closing `[ic option1="value1" option2="value2" ... /]`, and does not have any mandatory parameters, but it would not be useful to use it in this mode.

It, with greater profit, accepts the following options:

* `string` contains the text to display. If nothing is passed, the default text will be `email@example.com`.

* `fontsize` lets you assign the fontsize.

* `fcolor` is the color in the foreground.

* `falpha` is a value between 0 and 1. 0 indicates completely transparent while 1 indicates completely opaque.

* `bcolor` is the color in the background.

* `balpha` is a value between 0 and 1. 0 indicates completely transparent while 1 indicates completely opaque.

* `width` lets you assign the image width. It accepts an integer from 50 to 500 and the default value is 210.

* `height` lets you assign the image height. It accepts an integer from 20 to 500 and the default value is 27.

* `quality` is compression level: from 0 (no compression) to 9. The current default is 6.

* `position` tells where you can place the image: left, right or centered.

### Example Code

Below is an example without any parameters, one with only the string parameter and last with all the possible parameters.

|  |  |  |
|---|---|---|
| `[ic]` | ![](images/ic2.png) |
| `[ic string="My text to display"/]` | ![](images/ic3.png) |
| `[ic string="Hi GRAV people!" width="520" height="150" position="centered" fontsize="40" fcolor="006300" bcolor="f0f000" falpha="0.5" balpha="1" quality="6"]` | ![](images/ic4.png) |

## Credits

To work the plugin requires a [shortcode-code plugin](https://github.com/getgrav/grav-plugin-shortcode-core): a sincere thanks to the Grav team.

## To Do

- [ ] Verify placement of text within the image area when the `angle` is different from zero
- [ ] Add new fonts
- [ ] Create an image with text from an existing image from a file or URL

