/*
 * rep2expack - ネット動画のプレビュー
 */

// {{{ preview_video_youtube()

/*
 * YouTubeのプレビューを表示する
 *
 * @param String id
 * @param Element placeholder
 * @return void
 */
function preview_video_youtube(id, placeholder)
{
	var container = document.createElement('div');
	container.className = 'preview-video preview-video-youtube';

    var preview = document.createElement('iframe');
    preview.setAttribute('width', '425');
    preview.setAttribute('height', '350');
    preview.setAttribute('src', 'https://www.youtube.com/embed/' + id);
    preview.setAttribute('frameborder', '0');
    preview.setAttribute('allowfullscreen','');
    container.appendChild(preview);

	if (placeholder && placeholder.parentNode) {
		placeholder.parentNode.replaceChild(container, placeholder);
	} else {
		document.body.appendChild(container);
	}
}

// }}}
// {{{ preview_video_niconico()

/*
 * ニコニコ動画のプレビューを表示する
 *
 * @param String id
 * @param Element placeholder
 * @return void
 */
function preview_video_niconico(id, placeholder)
{
	var container = document.createElement('div');
	container.className = 'preview-video preview-video-niconico';

	var preview = document.createElement('iframe');
	preview.setAttribute('src', 'http://ext.nicovideo.jp/thumb/' + id);
	preview.setAttribute('width', '425');
	preview.setAttribute('height', '175');
	preview.setAttribute('frameborder', '0');
	preview.setAttribute('scrolling', 'auto');

	container.appendChild(preview);

	if (placeholder && placeholder.parentNode) {
		placeholder.parentNode.replaceChild(container, placeholder);
	} else {
		document.body.appendChild(container);
	}
}

// }}}

/*
 * Local Variables:
 * mode: javascript
 * coding: cp932
 * tab-width: 4
 * c-basic-offset: 4
 * indent-tabs-mode: t
 * End:
 */
/* vim: set syn=javascript fenc=cp932 ai noet ts=4 sw=4 sts=4 fdm=marker: */
