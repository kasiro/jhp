<?php

/**
 * Css Avatars Code
 */
class Avatars {

	public string $name = '';

	public function __con($user = '', $data_array, $arr, $name = '')
	{
		// * $data_array -> $Config
		// * $arr -> $Config_use
		if (!class_exists('str')){
			require '/home/kasiro/Документы/projects/testphp/user_modules/str.php';
		}
		if (!class_exists('Colors')) {
			require '/home/kasiro/Документы/projects/testphp/user_modules/Colors.php';
		}
		Colors::SetUpColors();
		$u = [];
		$o = 0;
		if (count($data_array) != count($arr)){
			echo count($data_array) . ' (Config) != ' . count($arr) . ' (Config use)' . PHP_EOL;
			system('notify-send "VkMessanger" "'.count($data_array) . ' (Config) != ' . count($arr) . ' (Config use)'.'" -i vk');
			return;
		}
		foreach ($data_array as $new_user) {
			$cur_user = array_filter($arr, fn($el) => $el['id'] === $new_user['id']);
			if (count($cur_user) > 0) {
				if ($cur_user[key($cur_user)]['use'] === true) {
					$u[] = $new_user;
				}
			}
		}
		if (count($u) > 0) {
			if ($user != '' && is_array($user)) {
				$this->print_user_av($user, $u);
			} else {
				$this->print_users_all_av($u);
			}
		}
		
		echo Colors::setColor($name, 'blue').' loaded users: ', Colors::setColor(count($u), 'red') . PHP_EOL;
	}

	public function setName($name){
		$this->name = $name;
	}

	private function replacer_av($user){
		if (isset($user['is_me'])) {
			$pattern = file_get_contents(__DIR__.'/patterns_avatars/my_pattern.txt');
		} else {
			if (str_contains($user['im_prebody_img'], 'userapi')){
				$pattern = file_get_contents(__DIR__.'/patterns_avatars/pattern.txt');
			} else {
				$pattern = file_get_contents(__DIR__.'/patterns_avatars/pattern_no.txt');
			}
		}
		$text = str::sprintf2($pattern, $user);
		return $text
	}

	public function print_users_av($data_array)
	{
		foreach ($data_array as $user) {
			$text = $this->replacer_av($user);
			$fileName = $user['rname'] . '_' . $user['rsurname'] . '.css';
			$rpath = __DIR__ . '/files/' . $fileName;
			file_put_contents($rpath, $text);
		}
	}

	public function print_users_all_av($data_array)
	{
		$text = '';
		$text .= '/*my code start*/' . "\r\n";
		$text .= file_get_contents(__DIR__.'/files/vars.css') . "\r\n";
		//FIXME: не хочет обрабатывать выяснить почему
		for ($i = 0; $i < count($data_array); $i++) {
			$user = $data_array[$i];
			if ($i != count($data_array) - 1) {
				$text .= $this->replacer_av($user) . "\r\n\r\n";
			} else {
				$text .= $this->replacer_av($user);
			}
		}
		$text .= "\r\n" . '/*my code end*/';
		$fileName = 'all_users_avatars.css';
		$rpath = __DIR__ . '/files/' . $fileName;
		file_put_contents($rpath, $text);
	}

	public function print_user_av($user_array, $data_array)
	{
		foreach ($data_array as $user) {
			if ($user['rname'] == $user_array['rname'] && $user['rsurname'] == $user_array['rsurname']) {
				$text = $this->replacer_av($user);
				break;
			}
		}
		$fileName = 'user_' . $user['rname'] . '_' . $user['rsurname'] . '_avatar.css';
		$rpath = __DIR__ . '/files/' . $fileName;
		file_put_contents($rpath, $text);
	}

}