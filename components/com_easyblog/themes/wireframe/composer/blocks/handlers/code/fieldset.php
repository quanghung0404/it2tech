<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_FONT_SIZE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field text-center">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <select class="form-control" data-code-fontsize>
                        <option value="10">10px</option>
                        <option value="11">11px</option>
                        <option value="12" selected="selected">12px</option>
                        <option value="13">13px</option>
                        <option value="14">14px</option>
                        <option value="16">16px</option>
                        <option value="18">18px</option>
                        <option value="20">20px</option>
                        <option value="24">24px</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_LANGUAGE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <select class="form-control" data-code-mode>
                        <option value="abap">ABAP</option>
                        <option value="actionscript">ActionScript</option>
                        <option value="ada">ADA</option>
                        <option value="apache_conf">Apache Conf</option>
                        <option value="asciidoc">AsciiDoc</option>
                        <option value="assembly_x86">Assembly x86</option>
                        <option value="autohotkey">AutoHotKey</option>
                        <option value="batchfile">BatchFile</option>
                        <option value="c9search">C9Search</option>
                        <option value="c_cpp">C/C++</option>
                        <option value="cirru">Cirru</option>
                        <option value="clojure">Clojure</option>
                        <option value="cobol">Cobol</option>
                        <option value="coffee">CoffeeScript</option>
                        <option value="coldfusion">ColdFusion</option>
                        <option value="csharp">C#</option>
                        <option value="css">CSS</option>
                        <option value="curly">Curly</option>
                        <option value="d">D</option>
                        <option value="dart">Dart</option>
                        <option value="diff">Diff</option>
                        <option value="dockerfile">Dockerfile</option>
                        <option value="dot">Dot</option>
                        <option value="erlang">Erlang</option>
                        <option value="ejs">EJS</option>
                        <option value="forth">Forth</option>
                        <option value="ftl">FreeMarker</option>
                        <option value="gherkin">Gherkin</option>
                        <option value="gitignore">Gitignore</option>
                        <option value="glsl">Glsl</option>
                        <option value="golang">Go</option>
                        <option value="groovy">Groovy</option>
                        <option value="haml">HAML</option>
                        <option value="handlebars">Handlebars</option>
                        <option value="haskell">Haskell</option>
                        <option value="haxe">haXe</option>
                        <option value="html" selected="selected">HTML</option>
                        <option value="html_ruby">HTML (Ruby)</option>
                        <option value="ini">INI</option>
                        <option value="jack">Jack</option>
                        <option value="jade">Jade</option>
                        <option value="java">Java</option>
                        <option value="javascript">JavaScript</option>
                        <option value="json">JSON</option>
                        <option value="jsoniq">JSONiq</option>
                        <option value="jsp">JSP</option>
                        <option value="jsx">JSX</option>
                        <option value="julia">Julia</option>
                        <option value="latex">LaTeX</option>
                        <option value="less">LESS</option>
                        <option value="liquid">Liquid</option>
                        <option value="lisp">Lisp</option>
                        <option value="livescript">LiveScript</option>
                        <option value="logiql">LogiQL</option>
                        <option value="lsl">LSL</option>
                        <option value="lua">Lua</option>
                        <option value="luapage">LuaPage</option>
                        <option value="lucene">Lucene</option>
                        <option value="makefile">Makefile</option>
                        <option value="matlab">MATLAB</option>
                        <option value="markdown">Markdown</option>
                        <option value="mel">MEL</option>
                        <option value="mysql">MySQL</option>
                        <option value="mushcode">MUSHCode</option>
                        <option value="nix">Nix</option>
                        <option value="objectivec">Objective-C</option>
                        <option value="ocaml">OCaml</option>
                        <option value="pascal">Pascal</option>
                        <option value="perl">Perl</option>
                        <option value="pgsql">pgSQL</option>
                        <option value="php">PHP</option>
                        <option value="powershell">Powershell</option>
                        <option value="prolog">Prolog</option>
                        <option value="properties">Properties</option>
                        <option value="protobuf">Protobuf</option>
                        <option value="python">Python</option>
                        <option value="r">R</option>
                        <option value="rdoc">RDoc</option>
                        <option value="rhtml">RHTML</option>
                        <option value="ruby">Ruby</option>
                        <option value="rust">Rust</option>
                        <option value="sass">SASS</option>
                        <option value="scad">SCAD</option>
                        <option value="scala">Scala</option>
                        <option value="smarty">Smarty</option>
                        <option value="scheme">Scheme</option>
                        <option value="scss">SCSS</option>
                        <option value="sh">SH</option>
                        <option value="sjs">SJS</option>
                        <option value="space">Space</option>
                        <option value="snippets">snippets</option>
                        <option value="soy_template">Soy Template</option>
                        <option value="sql">SQL</option>
                        <option value="stylus">Stylus</option>
                        <option value="svg">SVG</option>
                        <option value="tcl">Tcl</option>
                        <option value="tex">Tex</option>
                        <option value="text">Text</option>
                        <option value="textile">Textile</option>
                        <option value="toml">Toml</option>
                        <option value="twig">Twig</option>
                        <option value="typescript">Typescript</option>
                        <option value="vala">Vala</option>
                        <option value="vbscript">VBScript</option>
                        <option value="velocity">Velocity</option>
                        <option value="verilog">Verilog</option>
                        <option value="xml">XML</option>
                        <option value="xquery">XQuery</option>
                        <option value="yaml">YAML</option>
                   </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_THEME'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <select id="theme" class="form-control" data-code-theme>
                        <optgroup label="Bright">
                            <option value="ace/theme/chrome">Chrome</option>
                            <option value="ace/theme/clouds">Clouds</option>
                            <option value="ace/theme/crimson_editor">Crimson Editor</option>
                            <option value="ace/theme/dawn">Dawn</option>
                            <option value="ace/theme/dreamweaver">Dreamweaver</option>
                            <option value="ace/theme/eclipse">Eclipse</option>
                            <option value="ace/theme/github" selected="selected">GitHub</option>
                            <option value="ace/theme/solarized_light">Solarized Light</option>
                            <option value="ace/theme/textmate">TextMate</option>
                            <option value="ace/theme/tomorrow">Tomorrow</option>
                            <option value="ace/theme/xcode">XCode</option>
                            <option value="ace/theme/kuroir">Kuroir</option>
                            <option value="ace/theme/katzenmilch">KatzenMilch</option>
                        </optgroup>
                        <optgroup label="Dark">
                            <option value="ace/theme/ambiance">Ambiance</option>
                            <option value="ace/theme/chaos">Chaos</option>
                            <option value="ace/theme/clouds_midnight">Clouds Midnight</option>
                            <option value="ace/theme/cobalt">Cobalt</option>
                            <option value="ace/theme/idle_fingers">idle Fingers</option>
                            <option value="ace/theme/kr_theme">krTheme</option>
                            <option value="ace/theme/merbivore">Merbivore</option>
                            <option value="ace/theme/merbivore_soft">Merbivore Soft</option>
                            <option value="ace/theme/mono_industrial">Mono Industrial</option>
                            <option value="ace/theme/monokai">Monokai</option>
                            <option value="ace/theme/pastel_on_dark">Pastel on dark</option>
                            <option value="ace/theme/solarized_dark">Solarized Dark</option>
                            <option value="ace/theme/terminal">Terminal</option>
                            <option value="ace/theme/tomorrow_night">Tomorrow Night</option>
                            <option value="ace/theme/tomorrow_night_blue">Tomorrow Night Blue</option>
                            <option value="ace/theme/tomorrow_night_bright">Tomorrow Night Bright</option>
                            <option value="ace/theme/tomorrow_night_eighties">Tomorrow Night 80s</option>
                            <option value="ace/theme/twilight">Twilight</option>
                            <option value="ace/theme/vibrant_ink">Vibrant Ink</option>
                        </optgroup>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CODE_SHOW_GUTTER'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field text-center">
            <div class="row">
                <div class="col-lg-6 col-lg-offset-3">
                    <?php echo $this->html('grid.boolean', 'gutter', $data->show_gutter, 'gutter', 'data-code-gutter'); ?>
                </div>
            </div>
        </div>
    </div>
</div>