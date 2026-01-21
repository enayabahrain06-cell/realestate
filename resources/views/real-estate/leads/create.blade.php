@extends('layouts.real-estate.dashboard')

@section('title', 'Add Lead')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
<li class="breadcrumb-item active">Add Lead</li>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add New Lead</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('leads.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="source" class="form-label">Source</label>
                            <select class="form-select @error('source') is-invalid @enderror" id="source" name="source">
                                <option value="">Select Source</option>
                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="social" {{ old('source') == 'social' ? 'selected' : '' }}>Social Media</option>
                                <option value="ads" {{ old('source') == 'ads' ? 'selected' : '' }}>Advertisement</option>
                                <option value="walkin" {{ old('source') == 'walkin' ? 'selected' : '' }}>Walk-in</option>
                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="budget_min" class="form-label">Budget (Min)</label>
                            <div class="input-group">
                                <span class="input-group-text">AED</span>
                                <input type="number" class="form-control" id="budget_min" name="budget_min" 
                                       value="{{ old('budget_min') }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="budget_max" class="form-label">Budget (Max)</label>
                            <div class="input-group">
                                <span class="input-group-text">AED</span>
                                <input type="number" class="form-control" id="budget_max" name="budget_max" 
                                       value="{{ old('budget_max') }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="preferred_location" class="form-label">Preferred Location</label>
                            <input type="text" class="form-control" id="preferred_location" name="preferred_location" 
                                   value="{{ old('preferred_location') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="preferred_unit_type" class="form-label">Unit Type</label>
                            <select class="form-select" id="preferred_unit_type" name="preferred_unit_type">
                                <option value="">Select Type</option>
                                <option value="flat" {{ old('preferred_unit_type') == 'flat' ? 'selected' : '' }}>Flat/Apartment</option>
                                <option value="office" {{ old('preferred_unit_type') == 'office' ? 'selected' : '' }}>Office</option>
                                <option value="commercial" {{ old('preferred_unit_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="villa" {{ old('preferred_unit_type') == 'villa' ? 'selected' : '' }}>Villa</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bedrooms" class="form-label">Bedrooms</label>
                            <select class="form-select" id="bedrooms" name="bedrooms">
                                <option value="">Any</option>
                                <option value="1" {{ old('bedrooms') == '1' ? 'selected' : '' }}>1 Bedroom</option>
                                <option value="2" {{ old('bedrooms') == '2' ? 'selected' : '' }}>2 Bedrooms</option>
                                <option value="3" {{ old('bedrooms') == '3' ? 'selected' : '' }}>3 Bedrooms</option>
                                <option value="4" {{ old('bedrooms') == '4' ? 'selected' : '' }}>4 Bedrooms</option>
                                <option value="5" {{ old('bedrooms') == '5' ? 'selected' : '' }}>5+ Bedrooms</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_agent_id" class="form-label">Assign to Agent</label>
                        <select class="form-select" id="assigned_agent_id" name="assigned_agent_id">
                            <option value="">Unassigned</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('assigned_agent_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Lead</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

