<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateImportBookRequest;
use App\Http\Requests\UpdateImportBookRequest;
use App\Repositories\ImportBookRepository;
use App\Repositories\BookRepository;
use App\Repositories\StoreRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ImportBookController extends AppBaseController
{
    /** @var  ImportBookRepository */
    private $importBookRepository;
    private $bookRepository;
    private $storeRepository;

    public function __construct(
        ImportBookRepository $importBookRepo, 
        BookRepository $bookRepo,
        StoreRepository $storeRepo)
    {
        $this->importBookRepository = $importBookRepo;
        $this->bookRepository = $bookRepo;
        $this->storeRepository = $storeRepo;
    }

    /**
     * Display a listing of the ImportBook.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->importBookRepository->pushCriteria(new RequestCriteria($request));
        $importBooks = $this->importBookRepository->all();

        return view('import_books.index')
            ->with('importBooks', $importBooks);
    }

    /**
     * Show the form for creating a new ImportBook.
     *
     * @return Response
     */
    public function create()
    {
        $books = $this->bookRepository->all()->pluck('name', 'id');
        return view('import_books.create')
        ->with('books', $books);
    }

    public function create_file()
    {
        return view('import_books.create_file');
    }

    /**
     * File Export Code
     *
     * @var array
     */
	public function downloadExcel(Request $request, $type)
	{
        dd('Chuc nang dang duoc xay dung');
		$data = ImportBook::get()->toArray();
		return Excel::create('itsolutionstuff_example', function($excel) use ($data) {
			$excel->sheet('mySheet', function($sheet) use ($data)
	        {
				$sheet->fromArray($data);
	        });
		})->download($type);
	}

	/**
     * Import file into database Code
     *
     * @var array
     */
	public function importExcel(Request $request)
	{
        //dd($request);
		if($request->hasFile('import_file')){
            dd('Vo roi');
			$path = $request->file('import_file')->getRealPath();

			$data = Excel::load($path, function($reader) {})->get();

			if(!empty($data) && $data->count()){

				foreach ($data->toArray() as $key => $value) {
					if(!empty($value)){
						foreach ($value as $v) {		
							$insert[] = ['title' => $v['title'], 'description' => $v['description']];
						}
					}
				}

				
				if(!empty($insert)){
					ImportBook::insert($insert);
					return back()->with('success','Insert Record successfully.');
				}

			}
		}
		return back()->with('error','Please Check your file, Something is wrong there.');
	}


    /**
     * Store a newly created ImportBook in storage.
     *
     * @param CreateImportBookRequest $request
     *
     * @return Response
     */
    public function store(CreateImportBookRequest $request)
    {
        $input = $request->all();

        $book = $this->bookRepository->findWithoutFail($input['book_id']);

        if (empty($book)) {
            Flash::error('Import Book not found');

            return redirect(route('importBooks.index'));
        }

        $store = $this->storeRepository->findWithoutFail($input['book_id']);

        if (empty($store)) {
            $store = $this->storeRepository->create([
                'book_id' => $input['book_id'],
                'current_amount' => 0,
                'amount' => 0
            ]);
        }
        $store = $this->storeRepository->update([
            'book_id' => $input['book_id'],
            'current_amount' => $store->current_amount,
            'amount' => $store->amount
        ], $input['book_id']);
        $input['date'] = \Carbon\Carbon::today()->format('Y-m-d');     
        $importBook = $this->importBookRepository->create($input);

        Flash::success('Import Book saved successfully.');

        return redirect(route('importBooks.index'));
    }

    /**
     * Display the specified ImportBook.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $importBook = $this->importBookRepository->findWithoutFail($id);

        if (empty($importBook)) {
            Flash::error('Import Book not found');

            return redirect(route('importBooks.index'));
        }

        return view('import_books.show')->with('importBook', $importBook);
    }

    /**
     * Show the form for editing the specified ImportBook.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $importBook = $this->importBookRepository->findWithoutFail($id);
        if (empty($importBook)) {
            Flash::error('Import Book not found');

            return redirect(route('importBooks.index'));
        }
        $books = $this->bookRepository->all()->pluck('name', 'id');
        return view('import_books.edit')->with([
            'importBook'=> $importBook,
            'books'=> $books
        ]);
    }

    /**
     * Update the specified ImportBook in storage.
     *
     * @param  int              $id
     * @param UpdateImportBookRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateImportBookRequest $request)
    {
        $importBook = $this->importBookRepository->findWithoutFail($id);

        if (empty($importBook)) {
            Flash::error('Import Book not found');

            return redirect(route('importBooks.index'));
        }

        $importBook = $this->importBookRepository->update($request->all(), $id);

        Flash::success('Import Book updated successfully.');

        return redirect(route('importBooks.index'));
    }

    /**
     * Remove the specified ImportBook from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $importBook = $this->importBookRepository->findWithoutFail($id);

        if (empty($importBook)) {
            Flash::error('Import Book not found');

            return redirect(route('importBooks.index'));
        }

        $this->importBookRepository->delete($id);

        Flash::success('Import Book deleted successfully.');

        return redirect(route('importBooks.index'));
    }
}
