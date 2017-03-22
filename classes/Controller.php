<?php
/**
 *
 * User: vvpol
 * Date: 20.03.2017
 * Time: 21:41
 */

class Controller
{

    /**
     * Отображение документа
     * @param $file
     * @param array $data
     */
    private function display($file, $data = array())
    {
        if (sizeof($data) != 0){
            foreach ($data as $key => $val){
                $$key = $val;
            }
        }
        include $_SERVER['DOCUMENT_ROOT'] . '/views/' . $file;
    }

    /**
     * Перекодировка кирилицы
     * @param $s
     * @return string
     */
    function urlEncode($s){
        $s= strtr ($s, array (
            " "=> "%20", "а"=>"%D0%B0", "А"=>"%D0%90","б"=>"%D0%B1", "Б"=>"%D0%91",
            "в"=>"%D0%B2", "В"=>"%D0%92", "г"=>"%D0%B3", "Г"=>"%D0%93", "д"=>"%D0%B4", "Д"=>"%D0%94", "е"=>"%D0%B5", "Е"=>"%D0%95", "ё"=>"%D1%91", "Ё"=>"%D0%81", "ж"=>"%D0%B6", "Ж"=>"%D0%96", "з"=>"%D0%B7", "З"=>"%D0%97", "и"=>"%D0%B8", "И"=>"%D0%98", "й"=>"%D0%B9", "Й"=>"%D0%99", "к"=>"%D0%BA", "К"=>"%D0%9A", "л"=>"%D0%BB", "Л"=>"%D0%9B", "м"=>"%D0%BC", "М"=>"%D0%9C", "н"=>"%D0%BD", "Н"=>"%D0%9D", "о"=>"%D0%BE", "О"=>"%D0%9E", "п"=>"%D0%BF", "П"=>"%D0%9F", "р"=>"%D1%80", "Р"=>"%D0%A0", "с"=>"%D1%81", "С"=>"%D0%A1", "т"=>"%D1%82", "Т"=>"%D0%A2", "у"=>"%D1%83", "У"=>"%D0%A3", "ф"=>"%D1%84", "Ф"=>"%D0%A4", "х"=>"%D1%85", "Х"=>"%D0%A5", "ц"=>"%D1%86", "Ц"=>"%D0%A6", "ч"=>"%D1%87", "Ч"=>"%D0%A7", "ш"=>"%D1%88", "Ш"=>"%D0%A8", "щ"=>"%D1%89", "Щ"=>"%D0%A9", "ъ"=>"%D1%8A", "Ъ"=>"%D0%AA", "ы"=>"%D1%8B", "Ы"=>"%D0%AB", "ь"=>"%D1%8C", "Ь"=>"%D0%AC", "э"=>"%D1%8D", "Э"=>"%D0%AD", "ю"=>"%D1%8E", "Ю"=>"%D0%AE", "я"=>"%D1%8F", "Я"=>"%D0%AF"));
        return $s;
    }

    /**
     * Запрос страницы
     */
    public function index()
    {
        if (isset($_SESSION['msg'])){ // если в сессии есть сообщение, передаём рендеру
            $data = $_SESSION['msg'];
            unset($_SESSION['msg']);
        } else {
            $data = [];
        }
        $this-> display('url-frm.html', $data);
    }

    /**
     * Переход по короткому УРЛ
     * @param $url
     */
    public function go($url)
    {
        $model = new Model();
        $res = $model->findOne('short_url', $url);
        if ($res != []){  // существует
            header('Location: ' . $res['url']);
            exit;
        } else {
            $_SESSION['msg'] = [
                'error' => 'Короткая ссылка не найдена'
            ];
            header('Location: ' . core::$host);
            exit;
        }
    }

    /**
     * Проверка валидности УРЛ
     * @param $url
     * @return mixed
     */
    private function checkUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * Формирователь короткого УРЛ
     */
    public function make(){
        try {
            //
            $model = new Model();
            $model->lock();
            $url = $this->urlEncode(core::$data);
            if (mb_strlen($url) > 250){
                throw new Exception('Размер URL превышает допустимыый (250 символов после перекодировки)');
            }
            if (!$this->checkUrl($url)){
                throw new Exception('Неверный формат URL ' . $url . '=' . core::$data);
            }
            $res = $model->findOne('url', $url);
            if ($res !== []){  // Такой уже имеется
                echo json_encode([
                    'result' => core::$host . '/' . $res['short_url'],
                ]);
                return;
            } else { // Новый URL
                $res = $model->findOne('last', '');
                $r = $res['short_url'];
                $shortURL = str_split($r);
                // Генерим новый код
                $add = 0;  // перенос
                for ($i=sizeof($shortURL) - 1; $i >= 0; $i--){
                    $s = ord($shortURL[$i]);

                    if ($s == 90) {  // Z
                        $s = 97;  // a
                    } elseif ($s == 122) { // z
                        $s = 65;  // A
                        $add = 1;
                    } else {
                        $s++;
                    }
                    $shortURL[$i] = chr($s);
                    if ($add == 0) break;
                    $add = 0;
                }
                $strURL = implode('', $shortURL);
                $model->insert(['url' => $url, 'short_url' => $strURL]);
                echo json_encode([
                    'result' => core::$host . '/' . $strURL,
                ]);
            }
        } catch (Exception $e){
            echo json_encode([
                'error' => $e->getMessage(),
                ]);
        }
    }

}