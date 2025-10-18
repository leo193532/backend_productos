<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use OpenApi\Annotations as OA; //


class ProductoController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="sanctum",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     in="header",
     *     name="Authorization"
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/productos",
     *     summary="Listar productos",
     *     tags={"Productos"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function index()
    {
        $productos = Producto::all();

        if ($productos->isEmpty()) {
            return response()->json([
                'message' => 'No hay productos registrados.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'Listado de productos.',
            'data' => $productos,
        ], 200);

    }

     /**
     * @OA\Post(
     *     path="/api/productos",
     *     summary="Crear un nuevo producto",
     *     tags={"Productos"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "precio"},
     *             @OA\Property(property="nombre", type="string", example="Camisa deportiva"),
     *             @OA\Property(property="precio", type="number", format="float", example=19.99),
     *             @OA\Property(property="descripcion", type="string", example="Camisa cómoda para entrenar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto guardado correctamente"),
     *             @OA\Property(
     *                 property="producto",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nombre", type="string", example="Camisa deportiva"),
     *                 @OA\Property(property="precio", type="number", example=19.99),
     *                 @OA\Property(property="descripcion", type="string", example="Camisa cómoda para entrenar"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-28T10:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-28T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado"
     *     )
     * )
     */
    public function store(Request $request)
    {

        $rules = [
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:1000',
        ];

        $messages = [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',

            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',

            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no debe exceder los 1000 caracteres.',
        ];

        $request->validate($rules, $messages);

        $producto = Producto::create($request->all());

        return response()->json([
            'message' => 'Producto guardado correctamente',
            'producto' => $producto,
        ], 201);
    }

     /**
     * @OA\Get(
     *     path="/api/productos/{id}",
     *     summary="Obtener un producto por ID",
     *     tags={"Productos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="precio", type="number"),
     *             @OA\Property(property="descripcion", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Producto no encontrado"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function show($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto);
    }

     /**
     * @OA\Put(
     *     path="/api/productos/{id}",
     *     summary="Actualizar un producto",
     *     tags={"Productos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "precio"},
     *             @OA\Property(property="nombre", type="string", maxLength=255, example="Nuevo producto"),
     *             @OA\Property(property="precio", type="number", format="float", example=199.99),
     *             @OA\Property(property="descripcion", type="string", nullable=true, example="Nueva descripción")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Producto actualizado correctamente"),
     *     @OA\Response(response=404, description="Producto no encontrado"),
     *     @OA\Response(response=422, description="Error de validación"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $rules = [
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:1000',
        ];

        $messages = [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',

            'precio.required' => 'El precio del producto es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',

            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no debe exceder los 1000 caracteres.',
        ];

        $request->validate($rules, $messages);

        $producto->update($request->all());

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'producto' => $producto,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/productos/{id}",
     *     summary="Eliminar un producto",
     *     tags={"Productos"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Producto eliminado correctamente"),
     *     @OA\Response(response=404, description="Producto no encontrado"),
     *     @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        $producto->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }
}
