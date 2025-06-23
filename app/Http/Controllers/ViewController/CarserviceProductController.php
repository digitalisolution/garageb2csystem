<?php

namespace App\Http\Controllers\ViewController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CarserviceProduct;


class CarServiceProductController extends Controller
{
    /**
     * Fetch all available makes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMakes()
    {
        $carserviceProduct = new CarserviceProduct();
        $makes = $carserviceProduct->getMakes();

        return response()->json($makes);
    }

    /**
     * Fetch models based on the selected make.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModels(Request $request)
    {
        \Log::info('Received getModels request:', $request->all());

        try {
            $request->validate([
                'make' => 'required|string',
            ]);

            $carserviceProduct = new CarserviceProduct();
            $models = $carserviceProduct->getModels(['make' => $request->input('make')]);

            if (!$models) {
                \Log::warning('No models found for make:', ['make' => $request->input('make')]);
                return response()->json(['error' => 'No models found'], 404);
            }

            return response()->json($models);
        } catch (\Exception $e) {
            \Log::error('Error in getModels:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    /**
     * Fetch years based on the selected make and model.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getYears(Request $request)
    {
        $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
        ]);

        $carserviceProduct = new CarserviceProduct();
        $years = $carserviceProduct->getYears([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
        ]);

        return response()->json($years);
    }

    /**
     * Fetch engines based on the selected make, model, and year.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEngines(Request $request)
    {
        $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|string',
        ]);

        $carserviceProduct = new CarserviceProduct();
        $engines = $carserviceProduct->getEngines([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'year' => $request->input('year'),
        ]);

        return response()->json($engines);
    }
    public function service(Request $request)
    {
        // Fetch the makes from the CarserviceProduct model
        $carserviceProduct = new CarserviceProduct();
        $carMakes = $carserviceProduct->getMakes(); // This will retrieve the makes

        $make = $request->query('make');
        $model = $request->query('model');
        $year = $request->query('year');
        $engine = $request->query('engine');

        // Pass the makes along with other data to the view
        return view('service', compact('make', 'model', 'year', 'engine', 'carMakes'));
    }

}
