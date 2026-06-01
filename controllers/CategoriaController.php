<?php
/**
 * CategoriaController: Lógica para organizar los productos.
 */
require_once 'config/db.php';
require_once 'models/Categoria.php';

class CategoriaController {

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombreCat = $_POST['nombre_categoria'];

            $database = new Database();
            $db = $database->getConnection();
            $categoria = new Categoria($db);

            $categoria->nombre = $nombreCat;

            if($categoria->crear()) {
                echo "Categoría '$nombreCat' creada con éxito.";
                echo "<br><a href='views/registro_categoria.php'>Volver</a> | <a href='index.php?action=salir'>Salir</a>";
            } else {
                echo " Error al crear la categoría.";
            }
        }
    }
}
?>