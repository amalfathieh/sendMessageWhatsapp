<!DOCTYPE html>
<html>
<head>
    <title>جدول الطلاب</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th , td {
            text-align: center;
            padding: 8px;
        }
        tr:nth-child(even){
            background-color: #e0eaff;
        }
        th{
            background-color: #568af5;
            color: white;
        }
        .flex-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 30px;
        }

        .table-section {
            flex: 2.25;
            overflow-x: auto;
        }

        .form-section {
            flex: 0.75;
            padding: 10px;
            background-color: #f7f9ff;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        textarea {
            width: 100%;
            resize: vertical;
        }

        select, button {
            width: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div>

            @if(session('error'))
                <div style="color: red; padding: 10px; background: #ffebee; border: 1px solid red; border-radius: 5px; margin: 10px 0;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="color: red; padding: 10px; background: #ffebee; border: 1px solid red; border-radius: 5px; margin: 10px 0;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        @if (auth()->check())

            <div style="text-align: right; padding: 10px;">
                مرحباً، {{ auth()->user()->name }}
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-sm btn-outline-primary">تسجيل الخروج</button>
                </form>
            </div>

                <h2>📤 رفع ملف الطلاب</h2>
                <form action="{{ route('upload.excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="excel_file" required>
                    <button type="submit">عرض</button>
                </form>


                <div class="" style="margin: 10px">

                    {{-- عرض الجدول والرسائل فقط إذا تم رفع الملف --}}
                    @if(isset($students) && isset($headers))

                        <h2>جدول الطلاب</h2>
                        <form action="{{ route('send.messages') }}" method="POST">
                            <div class="flex-container">
                                <div class="table-section">

                                    <br>
                                    <button type="button" onclick="toggleCheckboxes(true)">✅ تحديد الكل</button>
                                    <button type="button" onclick="toggleCheckboxes(false)">❌ إلغاء التحديد</button>
                                    <br><br>

                                    @csrf
                                    <table >
                                        <thead>
                                        <tr>
                                            <th >تحديد</th>
                                            <th>-</th>
                                            @foreach ($headers as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($students as $i => $student)
                                            <tr>
                                                <td><input type="checkbox" name="selected[{{ $i }}]" value="1"></td>
                                                <td>{{ $loop->iteration }}</td>
                                                @foreach ($headers as $header)
                                                    <td >{{ $student[$header] ?? '' }}</td>
                                                @endforeach
                                                <input type="hidden" name="students[{{ $i }}]" value="{{ json_encode($student) }}">
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-section">
                                    <br>
                                    <label>نص الرسالة (استخدم $اسم_العمود كمتغير):</label><br>
                                    <textarea name="template" rows="8" cols="60" placeholder="مثال: الطالب $الاسم حصل على $الدرجة في $المادة"></textarea>
                                    <br>
                                    <label>ادخل التوكن:</label><br>
                                    <textarea name="token" rows="4" cols="60" required></textarea>

                                    <br><br>
                                    <label>اختر عمود رقم الهاتف:</label>
                                    <select name="phone_column" required>
                                        @foreach ($headers as $header)
                                            <option value="{{ $header }}">{{ $header }}</option>
                                        @endforeach
                                    </select>

                                    <br><br>

                                    <label>اختر الطريقة:</label>
                                    <select name="method" required>
                                        <option value="ultramsg">ultramsg</option>
                                        <option value="allMessage">allMessage</option>
                                    </select>

                                    <br><br>
                                    <button type="submit">إرسال الرسائل</button>
                                </div>
                            </div>
                        </form>

                    @endif
                </div>

                    <script>
                        function toggleCheckboxes(state) {
                            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                                checkbox.checked = state;
                            });
                        }

                        // التحقق من صيغة الملف قبل الرفع
                        document.querySelector('form[action="{{ route('upload.excel') }}"]').addEventListener('submit', function (e) {
                            const fileInput = document.querySelector('input[name="excel_file"]');

                            if (fileInput.files.length === 0) {
                                e.preventDefault();
                                alert("❌ الرجاء اختيار ملف Excel أولاً");
                                return;
                            }

                            const file = fileInput.files[0];
                            const allowedExtensions = /(\.xlsx|\.xls|\.csv)$/i;

                            if (!allowedExtensions.exec(file.name)) {
                                e.preventDefault();
                                alert("❌ الرجاء رفع ملف Excel فقط (xls, xlsx, csv)");
                                fileInput.value = '';
                            }
                        });
                    </script>


        @else
            {{-- ❌ المستخدم غير مسجل، إعادة التوجيه --}}
            <script>
                window.location.href = "{{ route('login') }}";
            </script>
        @endif
    </div>
</body>
</html>
