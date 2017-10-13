function startProgress(iFrame_src) {

	var iFrame = document.createElement('iframe');
	document.getElementsByTagName('body')[0].appendChild(iFrame);
	iFrame.src = iFrame_src;
}

function Zend_ProgressBar_Update(data) {

	document.getElementById('pg-percent').style.width = data.percent + '%';
	document.getElementById('pg-text-2').innerHTML = data.percent + '%';
}

function Zend_ProgressBar_Finish() {
	document.getElementById('pg-percent').style.width = '100%';

	document.getElementById('pg-text-2').innerHTML = 'Việc tải dữ liệu đã xong';
	jQuery("iframe").remove();
	jQuery("#progressbar").remove();

}