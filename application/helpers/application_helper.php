<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('validasi_sql')) {
	function validasi_sql($value)
	{
		if (is_array($value)) {
			return array_map('validasi_sql', $value);
		}

		if ($value === NULL) {
			return NULL;
		}

		if (is_bool($value) || is_int($value) || is_float($value)) {
			return $value;
		}

		return trim((string) $value);
	}
}

if ( ! function_exists('array_pilihan')) {
	function array_pilihan($var, $pilihan, $terpilih, $js = '')
	{
		echo '<select name="'.html_escape($var).'" id="'.html_escape($var).'" onChange="'.html_escape($js).'" class="form-control">';
		echo '<option value=""></option>';

		foreach ((array) $pilihan as $key => $value) {
			$selected = ((string) $key === (string) $terpilih) ? ' selected' : '';
			echo '<option value="'.html_escape($key).'"'.$selected.'>'.html_escape($value).'</option>';
		}

		echo '</select>';
	}
}

if ( ! function_exists('array_pilihan2')) {
	function array_pilihan2($var, $pilihan, $terpilih, $js = '')
	{
		echo '<select name="'.html_escape($var).'" id="'.html_escape($var).'" onChange="'.html_escape($js).'" class="text_input">';
		echo '<option value=""></option>';

		foreach ((array) $pilihan as $value) {
			$selected = ((string) $value === (string) $terpilih) ? ' selected' : '';
			echo '<option value="'.html_escape($value).'"'.$selected.'>'.html_escape($value).'</option>';
		}

		echo '</select>';
	}
}

if ( ! function_exists('array_pilihan3')) {
	function array_pilihan3($var, $pilihan, $terpilih, $js = '')
	{
		echo '<select name="'.html_escape($var).'" id="'.html_escape($var).'" onChange="'.html_escape($js).'" class="form-control">';

		foreach ((array) $pilihan as $value) {
			$selected = ((string) $value === (string) $terpilih) ? ' selected' : '';
			echo '<option value="'.html_escape($value).'"'.$selected.'>'.html_escape($value).'</option>';
		}

		echo '</select>';
	}
}

if ( ! function_exists('array_pilihan4')) {
	function array_pilihan4($var, $pilihan, $terpilih, $js = '')
	{
		echo '<select name="'.html_escape($var).'" id="'.html_escape($var).'" onChange="'.html_escape($js).'" class="select-produk">';
		echo '<option value="" disabled'.($terpilih === '' ? ' selected' : '').'>Pilih</option>';

		foreach ((array) $pilihan as $key => $value) {
			$selected = ((string) $key === (string) $terpilih) ? ' selected' : '';
			echo '<option value="'.html_escape($key).'"'.$selected.'>'.html_escape($value).'</option>';
		}

		echo '</select>';
	}
}

if ( ! function_exists('pages')) {
	function pages($halaman, $jmlhalaman, $url, $id = '')
	{
		$halaman = (int) $halaman;
		$jmlhalaman = (int) $jmlhalaman;
		$url = trim($url, '/');

		if ($halaman > 1) {
			$previous = $halaman - 1;
			echo '<li><a href="'.site_url($url.'/1'.$id).'"><i class="fa fa-angle-double-left"></i></a></li>';
			echo '<li><a href="'.site_url($url.'/'.$previous.$id).'"><i class="fa fa-angle-left"></i></a></li>';
		} else {
			echo '<li class="disabled"><a><i class="fa fa-angle-double-left"></i></a></li>';
			echo '<li class="disabled"><a><i class="fa fa-angle-left"></i></a></li>';
		}

		if ($halaman < $jmlhalaman) {
			$next = $halaman + 1;
			echo '<li><a href="'.site_url($url.'/'.$next.$id).'"><i class="fa fa-angle-right"></i></a></li>';
			echo '<li><a href="'.site_url($url.'/'.$jmlhalaman.$id).'"><i class="fa fa-angle-double-right"></i></a></li>';
		} else {
			echo '<li class="disabled"><a><i class="fa fa-angle-right"></i></a></li>';
			echo '<li class="disabled"><a><i class="fa fa-angle-double-right"></i></a></li>';
		}
	}
}

if ( ! function_exists('pages2')) {
	function pages2($halaman2, $jmlhalaman, $url, $id = '')
	{
		return pages($halaman2, $jmlhalaman, $url, $id);
	}
}

if ( ! function_exists('seo')) {
	function seo($s)
	{
		$remove = array('-', '/', '\\', ',', '.', '#', ':', ';', '\'', '"', '[', ']', '{', '}', ')', '(', '|', '`', '~', '!', '@', '%', '$', '^', '&', '*', '=', '?', '+');
		$s = str_replace($remove, '', (string) $s);

		return strtolower(str_replace(' ', '-', $s));
	}
}

if ( ! function_exists('validasi')) {
	function validasi($str, $tipe = 'sql')
	{
		$str = htmlspecialchars(stripslashes((string) $str), ENT_QUOTES, 'UTF-8');

		if ($tipe === 'sql') {
			return (int) preg_replace('/[^A-Za-z0-9]/', '', $str);
		}

		return preg_replace('/[\W]/', '', $str);
	}
}

if ( ! function_exists('re_html')) {
	function re_html($data)
	{
		return htmlspecialchars(strip_tags((string) $data), ENT_QUOTES, 'UTF-8');
	}
}

if ( ! function_exists('extension')) {
	function extension($path)
	{
		$file = pathinfo((string) $path);
		$fullPath = $file['dirname'].DIRECTORY_SEPARATOR.$file['basename'];

		return file_exists($fullPath) ? $file['basename'] : NULL;
	}
}

if ( ! function_exists('anti_injection')) {
	function anti_injection($value)
	{
		return validasi_sql($value);
	}
}

if ( ! function_exists('dateIndo')) {
	function dateIndo($date)
	{
		if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
			return '-';
		}

		$timestamp = is_numeric($date) ? (int) $date : strtotime($date);
		if ($timestamp === FALSE) {
			return $date;
		}

		$months = array(
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December',
		);

		$day = date('d', $timestamp);
		$month = $months[(int) date('n', $timestamp)];
		$year = date('Y', $timestamp);
		$time = date('H:i:s', $timestamp);

		return $time === '00:00:00'
			? $day.' '.$month.' '.$year
			: $day.' '.$month.' '.$year.' '.$time;
	}
}

if ( ! function_exists('format_rupiah')) {
	function format_rupiah($value)
	{
		return 'Rp '.number_format((float) $value, 0, ',', '.');
	}
}

if ( ! function_exists('format_ksh')) {
	function format_ksh($value)
	{
		return 'KSh '.number_format((float) $value, 2, '.', ',');
	}
}
