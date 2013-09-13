<?php

namespace engine;

/**
 * Klasa przetwarzająca pobrane od obiektów dane.
 *
 * Class output
 * @package engine
 */
class output {
    /**
     * @var \Twig_Environment Zmienna przechowująca obiekt środowiska Twig
     */
    public $twig;

    /**
     * @var array Tablica bufora danych wyjściowych
     */
    public $dataCache = array();

    /**
     * Konstruktor inicjujący środowisko Twig'a
     */
    public function __construct() {
        $config = core::$config;
        require_once './Twig/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register(true);
        $loader = new \Twig_Loader_Filesystem(array('./themes/'.$config['theme'].'/views'));
        $this->twig = new \Twig_Environment($loader, array('debug' => true));
        $this->twig->addExtension(new \Twig_Extension_Debug());
		return $this;
	}

    /**
     * Metoda zapisująca dane wyjściowe obiektów w buforze.
     *
     * @param $data Jeżeli $data jest tablicą zostaje ona w całości dopisana do tablicy bufora wyjściowego
     * @param null $value Jeżeli $data nie jest tablicą, do bufora wyjściowego zapisywane są dane w formacie $data=>$value
     * @return $this Zwrot obiektu klasy umożliwia łańcuchowy zapis funkcji
     */
    public function set($data, $value = null) {
		if (is_array($data)) {
            $this->dataCache = array_merge($this->dataCache, $data);
		} else {
            $this->dataCache[$data] = $value;
		}
		return $this;
	}

    /**
     * Metoda inicjująca zapisane do bufora wyjściowego danch każdego obiektu z aktualnej ściezki
     */
    public function prepareObjects() {
        $async = core::$input->isAjaxRequest();
        if (!count(core::$pathObjects)) return false;
        foreach (core::$pathObjects as $object) {
            $object->setOutput($async);
        }
    }

    /**
     * Metoda renderująca dowolny szablon Twig'a
     *
     * @param $file Plik z szablonem
     * @param $data Dodatkowe dane potrzebne szablonowi
     * @return mixed Zwracany HTML
     */
    public function render($file, $data) {
        return $this->twig->render($file, $data);
	}

    /**
     * Metoda wyświetlająca domyślny layout lub w przypadku AJAX'owego żądania zwracająca zakodowaną w formacie JSON
     * tablicę bufora danych wyjściowych
     */
    public function showSite() {
        $this->prepareObjects();
        $this->set('theme', core::$config['theme']);
        if (!core::$input->isAjaxRequest()){
            echo $this->twig->render('_layout.html.twig', $this->dataCache);
    } else {
            echo json_encode($this->dataCache);
        }
	}

    /**
     * Metoda zapsująca dane do sesji
     *
     * @deprecated Metoda powinna zostać usunięta
     *
     * @param $key klucz sesji
     * @param $value Wartość
     */
    public function setSession($key, $value) {
        $_SESSION[$key] = $value;
    }
}

?>