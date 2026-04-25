<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'vat_id' => $this->vat_id,
            'tax_id' => $this->tax_id,
            'pdf_main_color' => $this->pdf_main_color,
            'pdf_secondary_color' => $this->pdf_secondary_color,
            'logo' => $this->logo,
            'logo_path' => $this->logo_path,
            'stamp' => $this->stamp,
            'stamp_path' => $this->stamp_path,
            'unique_hash' => $this->unique_hash,
            'owner_id' => $this->owner_id,
            'slug' => $this->slug,
            'address' => $this->when($this->address()->exists(), function () {
                return new AddressResource($this->address);
            }),
            'roles' => RoleResource::collection($this->roles),
        ];
    }
}
