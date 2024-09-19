<?php

/**
 * Menghentikan eksekusi program dan menampilkan data yang diberikan.
 *
 * @param mixed ...$data Data yang ingin ditampilkan menggunakan fungsi d().
 * @return void
 */
function dd(...$data)
{
    foreach($data as $d){
        d($d);
    }
    die;
}

/**
 * Menampilkan view dari file PHP yang diberikan dan mengekstrak data ke variabel.
 *
 * @param string $view Nama file view (tanpa ekstensi .php) yang akan dimuat.
 * @param array $data Data yang akan diekstrak sebagai variabel untuk digunakan di dalam view.
 * @return void
 */
function view($view, $data = [])
{
    // Ekstrak array data menjadi variabel
    extract($data);

    // Sertakan file view
    require_once 'views/' . $view . '.php';
}

/**
 * Memuat file view dengan pengecualian jika file tidak ditemukan.
 *
 * @param string $view Nama file view (dengan path relatif) yang akan dimuat.
 * @param array $data Data yang akan diekstrak ke dalam view.
 * @throws Throwable Jika file view tidak ditemukan atau terjadi kesalahan lain.
 * @return void
 */
function requireView($view, $data = []){
    extract($data);
    try {
        require_once VIEWS . $view;
    } catch (\Throwable $th) {
        throw $th;
        die;
    }
}

/**
 * Memeriksa apakah nilai adalah null atau false.
 *
 * @param mixed $value Nilai yang akan diperiksa.
 * @return bool Mengembalikan true jika nilai adalah null atau false, sebaliknya false.
 */
function isNullOrFalse($value){
    if (is_null($value)) {
        return true;
    }

    if ($value === false) {
        return true;
    }

    return false;
}

/**
 * Memanggil method dari class tertentu jika class dan method tersebut ada.
 *
 * @param string $className Nama class yang ingin dipanggil.
 * @param string $methodName Nama method dari class yang ingin dipanggil.
 * @return callable Fungsi anonim yang bisa dipanggil untuk mengeksekusi method tersebut.
 * @throws Exception Jika class atau method tidak ditemukan.
 */
function invokeClass($className, $methodName) : callable
{
    if (!class_exists($className)){
        throw new Exception("Target class '{$className}' tidak ada!");
    }
    if (!method_exists($className, $methodName)){
        throw new Exception("Target method '{$methodName}' dari class {$className} tidak ada!");
    }
    return function(...$data) use ($className, $methodName){
        $controller = new $className();
        call_user_func_array([$controller, $methodName], $data);
    };
}

/**
 * Memanggil controller dan action berdasarkan nama yang diberikan.
 *
 * @param string $controllerName Nama controller yang ingin dipanggil.
 * @param string $actionName Nama action (method) dalam controller.
 * @return callable Fungsi anonim yang dapat dipanggil untuk mengeksekusi action tersebut.
 * @throws Exception Jika controller atau method tidak ditemukan.
 */
function callController(string $controllerName, string $actionName) : callable
{
    if (!class_exists($controllerName)) {
        throw new Exception("Controller '{$controllerName}' tidak ditemukan!");
    }

    if (!method_exists($controllerName, $actionName)) {
        throw new Exception("Action atau method '{$actionName}' dari controller '{$controllerName}' tidak ditemukan!");
    }

    return function (...$params) use ($controllerName, $actionName) {
        $controller = new $controllerName();
        return call_user_func_array([$controller, $actionName], $params);
    };
}

/**
 * Menghasilkan string acak menggunakan generator angka pseudorandom yang aman secara kriptografi.
 *
 * @param int $length Panjang string yang diinginkan.
 * @param string $keyspace Karakter yang digunakan untuk membentuk string.
 * @return string String acak yang dihasilkan.
 * @throws RangeException Jika panjang yang diberikan kurang dari 1.
 */
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Panjang harus bilangan bulat positif");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

/**
 * Memformat nilai harga menjadi format yang lebih rapi, menghilangkan nol setelah koma.
 *
 * @param float|int $value Nilai harga yang ingin diformat.
 * @return string Nilai harga yang sudah diformat.
 */
function formatPriceValue($value){
    return rtrim(rtrim(number_format($value, 2), '0'), '.');
}

/**
 * Mengarahkan browser ke URL tertentu menggunakan JavaScript.
 *
 * @param string $urlTarget URL yang menjadi target pengalihan.
 * @return void
 */
function jsRedirect(string $urlTarget){
    echo "<script>
    window.location.href = '$urlTarget'    
    </script>";
}

/**
 * Mengambil nilai variabel global berdasarkan nama variabel.
 *
 * @param string $varName Nama variabel global yang ingin diambil.
 * @return mixed Nilai dari variabel global atau null jika tidak ada.
 */
function getGlobalVar($varName) {
    global $$varName;
    return isset($$varName) ? $$varName : null;
}

/**
 * Mendapatkan index dari array berdasarkan nilai tertentu.
 *
 * @param array $array Array yang ingin dicari.
 * @param string $searchKey Kunci yang ingin dicari dalam array.
 * @param mixed $searchValue Nilai yang sesuai dengan kunci yang dicari.
 * @return int|string|null Index dari nilai yang ditemukan atau null jika tidak ditemukan.
 */
function getIndexByValue($array, $searchKey, $searchValue) {
    $values = array_column($array, $searchKey);
    return array_search($searchValue, $values);
}

?>
