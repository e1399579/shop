<?php
/**
 * Copyright (c) 2015-2016 http://ridersam.cn All rights reserved.
 * Date   : 2016/8/14 0014
 * Time   : 10:27
 * Author : ridersam <e1399579@163.com>
 */
/**
 * JSON消息
 * @param string $code
 * @param string $message
 * @param array $data
 * @return string
 */
function jsonMess($code, $message='', $data=array()) {
	return json_encode(compact('code', 'message', 'data'), JSON_UNESCAPED_UNICODE);
}
