
<div>
    <h1>Create Visit</h1>

  
    <form action="{{ route('bezoeken.store') }}" method="POST">
        @csrf

        <div>
            <label for="visitor_id">Visitor:</label>
            <select name="visitor_id" id="visitor_id" required>
                <option value="">Select Visitor</option>
                @foreach ($visitors as $visitor)
                <option value="{{ $visitor->id }}" {{ old('visitor_id') == $visitor->id ? 'selected' : '' }}>
                    {{ $visitor->name }}
                </option>
                @endforeach
            </select>
            @error('visitor_id') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="host_employee_id">Host Employee:</label>
            <select name="host_employee_id" id="host_employee_id" required>
                <option value="">Select Employee</option>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" {{ old('host_employee_id') == $employee->id ? 'selected' : '' }}>
                    {{ $employee->name }}
                </option>
                @endforeach
            </select>
            
        </div>

        <div>
            <label for="status">Status:</label>
            <input type="text" name="status" id="status" value="{{ old('status') }}" required>
            

        <div>
            <label for="expected_arrival_time">Expected Arrival Time:</label>
            <input type="datetime-local" name="expected_arrival_time" id="expected_arrival_time" value="{{ old('expected_arrival_time') }}" required>
           
        </div>

        <div>
            <label for="expected_departure_time">Expected Departure Time:</label>
            <input type="datetime-local" name="expected_departure_time" id="expected_departure_time" value="{{ old('expected_departure_time') }}" required>
            
        </div>

        <div>
            <label for="reason_of_visit">Reason of Visit:</label>
            <textarea name="reason_of_visit" id="reason_of_visit">{{ old('reason_of_visit') }}</textarea>
           
        </div>

        <div>
            <label for="badge_sent">Badge Sent:</label>
            <input type="checkbox" name="badge_sent" id="badge_sent" {{ old('badge_sent') ? 'checked' : '' }}>
            
        </div>

        <button type="submit">Create Visit</button>
    </form>
</div>
@endsection