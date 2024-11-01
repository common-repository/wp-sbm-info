<?php
function sbm_info_execute($url = null, $title = null)
{
	if (!$url) $url = get_permalink();
	if (!$title) $title = get_the_title();
	WP_SBM_Info::getInstance()->execute($url, $title);
}

function sbm_info_all($url = null, $title = null)
{
	if (!$url) $url = get_permalink();
	if (!$title) $title = get_the_title();
	return WP_SBM_Info::getInstance()->getAll($url, $title);
}

function sbm_info_count($url = null)
{
	if (!$url) $url = get_permalink();
	return WP_SBM_Info::getInstance()->setUrl($url)->getCount();
}

function sbm_info_unit($url = null)
{
	if (!$url) $url = get_permalink();
	return WP_SBM_Info::getInstance()->setUrl($url)->getUnit();
}

function sbm_info_rank($url = null)
{
	if (!$url) $url = get_permalink();
	return WP_SBM_Info::getInstance()->setUrl($url)->getRank();
}

function sbm_info_comments($url = null)
{
	if (!$url) $url = get_permalink();
	return WP_SBM_Info::getInstance()->setUrl($url)->getComments();
}

function sbm_info_entry_url($url = null)
{
	if (!$url) $url = get_permalink();
	return WP_SBM_Info::getInstance()->setUrl($url)->getEntryUrl();
}

function sbm_info_add_url($url = null, $title = null)
{
	if (!$url) $url = get_permalink();
	if (!$title) $title = get_the_title();
	return WP_SBM_Info::getInstance()->setUrl($url)->setTitle($title)->getAddUrl();
}