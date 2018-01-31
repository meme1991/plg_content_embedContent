<?php
# @Author: SPEDI srl
# @Date:   30-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 31-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (c) SPEDI srl

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if($item['overlay'] == 0) : ?>
	<?php // test video ?>
	<?php if(strpos($item['url'], 'facebook')): ?>
		<?php //$link = 'https://www.facebook.com/plugins/video.php?href='.$item['url'].'&show_text=0&width=560'; ?>
		<?php $playerOption = 'scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"'; ?>
	<?php elseif(strpos($item['url'], 'youtube')): ?>
		<?php $link = 'https://www.youtube.com/embed/'.substr($item['url'], strrpos($item['url'], '=')+1, strlen($item['url'])); ?>
		<?php $playerOption = 'allow="autoplay; encrypted-media" allowfullscreen'; ?>
	<?php elseif(strpos($item['url'], 'vimeo')): ?>
		<?php $link = 'https://player.vimeo.com/video/'.substr($item['url'], strrpos($item['url'], '/')+1, strlen($item['url'])); ?>
		<?php $playerOption = 'webkitallowfullscreen mozallowfullscreen allowfullscreen'; ?>
	<?php elseif(strpos($item['url'], 'google')): ?>
		<?php $link = $item['url']; ?>
		<?php $playerOption = 'allowfullscreen'; ?>
	<?php endif; ?>
	<?php
		switch ($item['align']) {
			case 0  : $align = ''; break;
			case 1  : $align = 'float:left;margin:15px 15px 15px 0;'; break;
			case 2  : $align = 'float:right;margin:15px 0 15px 15px;'; break;
			default : $align = ''; break;
		}
	 ?>
	<?php if(strpos($item['url'], 'facebook')): ?>
		<?php require_once(__DIR__ . '/../../dist/facebook/fb.php');  ?>
		<div class="fb-video mb-3" data-href="<?= $item['url'] ?>" style="width:<?= $item['width'] ?>;<?= $align ?>" data-show-text="false"></div>
	<?php else: ?>
		<iframe style="width:<?= $item['width'] ?>;height:<?= $item['height'] ?>;<?= $align ?>" src="<?= $link ?>" frameborder="0" <?= $playerOption ?>></iframe>
	<?php endif; ?>
<?php else: ?>
	<?php if($item['url'] == '' OR $item['label'] == '') return; ?>
	<?php // lity ?>
	<?php $extensionPath = '/templates/'.$tmpl.'/dist/lity/'; ?>
	<?php JHtml::_('jquery.framework'); ?>
	<?php if(file_exists(JPATH_SITE.$extensionPath)): ?>
		<?php $document->addStyleSheet(JUri::base(true).'/templates/'.$tmpl.'/dist/lity/lity.min.css'); ?>
		<?php $document->addScript(JUri::base(true).'/templates/'.$tmpl.'/dist/lity/lity.min.js'); ?>
	<?php else: ?>
		<?php $document->addStyleSheet(JUri::base(true).'/plugins/content/'.$this->plg_name.'/dist/lity/lity.min.css'); ?>
		<?php $document->addScript(JUri::base(true).'/plugins/content/'.$this->plg_name.'/dist/lity/lity.min.js'); ?>
	<?php endif; ?>
	<a href="<?= $item['url'] ?>" data-lity><?= $item['label'] ?><i class="far fa-external-link ml-1" style="font-size:15px;color:#007bff;"></i></a>
<?php endif; ?>
