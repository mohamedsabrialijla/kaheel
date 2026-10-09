<?php

namespace App\Http\Controllers\Web\MasterData;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Language;
use App\Models\ItemsCategories;
use App\Models\ItemsTypes;



class ItemsCategoriesController extends Controller
{
    protected const GENDER_SLUGS = ['male', 'female'];

    protected const GENDER_LABELS = [
        'male' => ['ar' => 'رجالي', 'en' => "Men's"],
        'female' => ['ar' => 'نسائي', 'en' => "Women's"],
    ];

     public function __construct() {
        $this->middleware('auth', ['except' => ['login', 'register']]);

        $this->locales = Language::all();
        view()->share([
            'locales' => $this->locales,

        ]);
    }

    public function image_extensions(){
        return array('jpg','png','jpeg','gif','bmp');
    }

   


    public function create(Request $request)
    {
        // dd($request->all());
      
        $id = auth('admin')->id();

        $validator = Validator::make($request->all(), [
           
            'type' => 'required',
            'order' => 'required',
        ]);


        if($request->type == 'country'){
 
            $validator = Validator::make($request->all(), [
                'code' => 'required',
            ]);

        }


        if($request->type == 'city'){
 
            $validator = Validator::make($request->all(), [
                'country' => 'required',
            ]);

        }



        if($request->type == 'brand'){
 
            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);

        }


        if($request->type == 'category'){
 
            $validator = Validator::make($request->all(), [
                'icon' => 'required',
            ]);

        }






        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            // $roles['description_' . $locale->lang] = 'required';
        }


        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = New ItemsCategories;
                $item->type_id = ItemsTypes::where('name',$request->type)->pluck('id')->first();
                $item->category = $request->category;
                $item->country = $request->country;
                $item->auto = $request->auto;
                $item->code = $request->code;
                $item->icon = $request->icon;
                $item->slug = strtoupper(substr($request->get('name_en'), 0, 2));
                $item->order = $request->order;
                $item->created_by = $id;
                
             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    // $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                   if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/country');

                        $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("logo")->move($destinationPath, $fileName);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                if(isset($fileName)){$item->logo='uploads/country/'.$fileName;}



                $item->save();

                if ($request->type === 'category') {
                    $this->syncCategoryGendersAndSubcategories($item, $request);
                }

                $message ="Doing successfully";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }



    public function edit(Request $request)
    {
      // dd($request->all());
        $id = auth('admin')->id();
        $item = ItemsCategories::query()->findOrFail($request->Item_id);


        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'order' => 'required',
        ]);

        foreach ($this->locales as $locale) {
            $roles['name_' . $locale->lang] = 'required';
            // $roles['description_' . $locale->lang] = 'required';
        }


        if($request->type == 'country'){
 
            $validator = Validator::make($request->all(), [
                'code' => 'required',
            ]);

        }


        if($request->type == 'brand'){
 
            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);

        }


        if($request->type == 'city'){
 
            $validator = Validator::make($request->all(), [
                'country' => 'required',
            ]);

        }

        if($request->type == 'category'){
 
            $validator = Validator::make($request->all(), [
                'icon' => 'required',
            ]);

        }



       





        if ($validator->fails()) {
            return mainResponse(false, '' , null, 203, 'items',$validator);
        }else{

 
                
                $item = ItemsCategories::findOrFail($request->Item_id);
                $item->type_id = ItemsTypes::where('name',$request->type)->pluck('id')->first();
                $item->category = $request->category;
                $item->created_by = $id;
                $item->country = $request->country;
                $item->auto = $request->auto;
                $item->code = $request->code;
                $item->icon = $request->icon;
                $item->slug = strtoupper(substr($request->get('name_en'), 0, 2));;
                $item->order = $request->order;



             
                foreach ($this->locales as $locale) {
                    $item->translateOrNew($locale->lang)->name = $request->get('name_' . $locale->lang);
                    // $item->translateOrNew($locale->lang)->description = $request->get('description_' . $locale->lang);
                }


                 if($request->hasFile('logo') && $request->file("logo")!='')
                {
                    if ($request->file("logo")->isValid())
                    {
                        $destinationPath=public_path('uploads/country');

                        $extension=strtolower($request->file("logo")->getClientOriginalExtension());
                        $array= $this->image_extensions();
                        if(in_array($extension,$array))
                        {
                            $fileName=uniqid().'.'.$extension;
                            $request->file("logo")->move($destinationPath, $fileName);
                        }else{
                            return response()->json(['message'=> 'file:jpg,png,jpeg,gif,bmp,pdf', 'code'=>201 , 'status'=>false]);
                        }
                    }
                }
                if(isset($fileName)){$item->logo='uploads/country/'.$fileName;}





                $item->save();

                if ($request->type === 'category') {
                    $this->syncCategoryGendersAndSubcategories($item, $request);
                }

                $message ="Doing successfully";
                return mainResponse(true, $message , $item, 200, 'items','');

               

       
        }
    }

    protected function typeId(string $name): ?int
    {
        return ItemsTypes::where('name', $name)->value('id');
    }

    protected function parseSubcategoryRows(Request $request, string $key): array
    {
        $raw = $request->input($key);
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($raw)) {
            return [];
        }

        $rows = [];
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nameAr = trim((string) ($row['name_ar'] ?? ''));
            $nameEn = trim((string) ($row['name_en'] ?? ''));
            if ($nameAr === '' && $nameEn === '') {
                continue;
            }
            $rows[] = [
                'id' => isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
            ];
        }

        return $rows;
    }

    protected function ensureGender(ItemsCategories $category, string $slug, int $genderTypeId, int $adminId): ItemsCategories
    {
        $labels = self::GENDER_LABELS[$slug];
        $gender = ItemsCategories::where('type_id', $genderTypeId)
            ->where('category', $category->id)
            ->where('slug', $slug)
            ->first();

        if (!$gender) {
            $gender = new ItemsCategories;
            $gender->type_id = $genderTypeId;
            $gender->category = $category->id;
            $gender->slug = $slug;
            $gender->order = $slug === 'male' ? 1 : 2;
            $gender->created_by = $adminId;
            $gender->status = 'active';
        }

        foreach ($this->locales as $locale) {
            $gender->translateOrNew($locale->lang)->name = $labels[$locale->lang] ?? $labels['ar'];
        }

        $gender->save();

        return $gender;
    }

    protected function syncSubcategoryList(ItemsCategories $gender, array $rows, int $subcategoryTypeId, int $adminId): void
    {
        $keptIds = [];

        foreach ($rows as $index => $row) {
            if (!empty($row['id'])) {
                $sub = ItemsCategories::where('id', $row['id'])
                    ->where('type_id', $subcategoryTypeId)
                    ->where('category', $gender->id)
                    ->first();
                if (!$sub) {
                    continue;
                }
            } else {
                $sub = new ItemsCategories;
                $sub->type_id = $subcategoryTypeId;
                $sub->category = $gender->id;
                $sub->created_by = $adminId;
                $sub->status = 'active';
            }

            $sub->order = $index + 1;
            $sub->slug = strtoupper(substr($row['name_en'] ?: $row['name_ar'], 0, 2));

            foreach ($this->locales as $locale) {
                $sub->translateOrNew($locale->lang)->name = $row['name_' . $locale->lang] ?? '';
            }

            $sub->save();
            $keptIds[] = $sub->id;
        }

        ItemsCategories::where('type_id', $subcategoryTypeId)
            ->where('category', $gender->id)
            ->when(count($keptIds) > 0, fn ($q) => $q->whereNotIn('id', $keptIds))
            ->delete();
    }

    protected function syncCategoryGendersAndSubcategories(ItemsCategories $category, Request $request): void
    {
        $genderTypeId = $this->typeId('gender');
        $subcategoryTypeId = $this->typeId('subcategory');
        if (!$genderTypeId || !$subcategoryTypeId) {
            return;
        }

        $adminId = auth('admin')->id();
        $maleRows = $this->parseSubcategoryRows($request, 'subcategories_male');
        $femaleRows = $this->parseSubcategoryRows($request, 'subcategories_female');

        $maleGender = $this->ensureGender($category, 'male', $genderTypeId, $adminId);
        $femaleGender = $this->ensureGender($category, 'female', $genderTypeId, $adminId);

        $this->syncSubcategoryList($maleGender, $maleRows, $subcategoryTypeId, $adminId);
        $this->syncSubcategoryList($femaleGender, $femaleRows, $subcategoryTypeId, $adminId);
    }

    protected function categoryGenderPayload(int $categoryId): array
    {
        $genderTypeId = $this->typeId('gender');
        $subcategoryTypeId = $this->typeId('subcategory');
        $payload = [
            'subcategories_male' => [],
            'subcategories_female' => [],
        ];

        if (!$genderTypeId || !$subcategoryTypeId) {
            return $payload;
        }

        $genders = ItemsCategories::with(['subcategories' => function ($q) use ($subcategoryTypeId) {
            $q->where('type_id', $subcategoryTypeId)->orderBy('order');
        }])
            ->where('type_id', $genderTypeId)
            ->where('category', $categoryId)
            ->whereIn('slug', self::GENDER_SLUGS)
            ->get()
            ->keyBy('slug');

        foreach (self::GENDER_SLUGS as $slug) {
            $gender = $genders->get($slug);
            if (!$gender) {
                continue;
            }
            $key = $slug === 'male' ? 'subcategories_male' : 'subcategories_female';
            foreach ($gender->subcategories as $sub) {
                $row = ['id' => $sub->id, 'name_ar' => '', 'name_en' => ''];
                foreach ($sub->translations as $tr) {
                    $row['name_' . $tr->locale] = $tr->name;
                }
                $payload[$key][] = $row;
            }
        }

        return $payload;
    }


    public function getAll(Request $request)
    {

         $id = auth('admin')->id();

            $items = ItemsCategories::with('type','country','category');


            if ($request->has('search') && !empty($request->search)) {
                $items->where(function ($query) use ($request) {
                    $query->whereTranslationLike('name', '%' . $request->search . '%')
                          ->orWhereHas('country', function ($q) use ($request) {
                              $q->whereTranslationLike('name', '%' . $request->search . '%');
                          });
                });
            }



            if($request->has('type') && !empty($request->type)) {
                $items->whereHas('type', function($query) use ($request) {
                    $query->where('name',$request->type);
                });
            }



            if ($request->has('parent_id') && !empty($request->parent_id)) {
                $items->where(function ($query) use ($request) {
                    $query->where('category',$request->parent_id );
                          
                });
            }


            if ($request->has('parent_id2') && !empty($request->parent_id2)) {
                $items->where(function ($query) use ($request) {
                    $query->where('country',$request->parent_id2 );
                          
                });
            }




            if(isset($request->pagination) && $request->pagination == 1) {
                $items = $items->orderBy('order','Asc')->paginate(10); 
            } else {
                $items = $items->orderBy('order','Asc')->get();
            }

            $message = "success return";

            return mainResponse(true, $message, $items, 200, 'items', '');
    }



    public function getById(Request $request)
    {
        $items = ItemsCategories::with('type','country','category')->where('id',$request->ID)->first();

        if ($items && $items->type && $items->type->name === 'category') {
            $items = $items->toArray();
            $items = array_merge($items, $this->categoryGenderPayload((int) $request->ID));
        }

        $message ="success return";
        return mainResponse(true, $message , $items, 200, 'items',''); 
    }

    public function delete($id)
    {
        $item = ItemsCategories::with('type')->find($id);
        if ($item && $item->type && $item->type->name === 'category') {
            $genderTypeId = $this->typeId('gender');
            $subcategoryTypeId = $this->typeId('subcategory');
            if ($genderTypeId && $subcategoryTypeId) {
                $genderIds = ItemsCategories::where('type_id', $genderTypeId)
                    ->where('category', $id)
                    ->pluck('id');
                if ($genderIds->isNotEmpty()) {
                    ItemsCategories::where('type_id', $subcategoryTypeId)
                        ->whereIn('category', $genderIds)
                        ->delete();
                    ItemsCategories::whereIn('id', $genderIds)->delete();
                }
            }
        }

        if(ItemsCategories::where('id',$id)->delete()){
            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');
        }else{
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
        }
        
    }



 
    function deleteAllItem(Request $request){

        $selectedItems = explode(',', request()->query('selectedItems'));
        $action = $request->action ;
        

        try {

            if($action == 'delete'){
            ItemsCategories::whereIn('id',$selectedItems)->delete();
            }elseif ($action == 'active') {
                ItemsCategories::whereIn('id',$selectedItems)->update(['status'=>'active']);
            }else{
                ItemsCategories::whereIn('id',$selectedItems)->update(['status'=>'not_active']);
            }

            $message ="success return";
            return mainResponse(true, $message , '', 200, 'items','');

 
        } catch (Exception $e) {
            $message ="Error happens";
            return mainResponse(false, $message , '', 203, 'items','');
            
        }



    }

    

}