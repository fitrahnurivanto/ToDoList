import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../settings.dart';
import '../auth/login.dart';
import 'package:intl/intl.dart';

class HomePage extends StatefulWidget {
  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  List<dynamic> _todos = [];
  bool _isLoading = true;
  String _userName = '';
  String _userEmail = '';

  @override
  void initState() {
    super.initState();
    _loadUserData();
    _fetchTodos();
  }

  Future<void> _loadUserData() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      _userName = prefs.getString('user_name') ?? 'User';
      _userEmail = prefs.getString('user_email') ?? '';
    });
  }

  Future<void> _fetchTodos() async {
    setState(() {
      _isLoading = true;
    });

    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('$base_url/todos'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        setState(() {
          _todos = data['data'] ?? [];
        });
      } else {
        _showSnackBar('Failed to fetch todos', Colors.red);
      }
    } catch (e) {
      _showSnackBar('Network error: $e', Colors.red);
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  // Method baru untuk update status
  Future<void> _updateStatus(int todoId, String newStatus) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      final response = await http.patch(
        Uri.parse('$base_url/todos/$todoId/status'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: json.encode({
          'status': newStatus,
        }),
      );

      if (response.statusCode == 200) {
        _showSnackBar('Status updated successfully', Colors.green);
        _fetchTodos(); // Refresh list
      } else {
        _showSnackBar('Failed to update status', Colors.red);
      }
    } catch (e) {
      _showSnackBar('Network error: $e', Colors.red);
    }
  }

  Future<void> _logout() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      await http.post(
        Uri.parse('$base_url/logout'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      // Clear stored data
      await prefs.clear();

      // Navigate to login
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => LoginPage()),
      );
    } catch (e) {
      _showSnackBar('Logout error: $e', Colors.red);
    }
  }

  Future<void> _deleteTodo(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      final response = await http.delete(
        Uri.parse('$base_url/todos/$id'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        _showSnackBar('Todo deleted successfully', Colors.green);
        _fetchTodos();
      } else {
        _showSnackBar('Failed to delete todo', Colors.red);
      }
    } catch (e) {
      _showSnackBar('Network error: $e', Colors.red);
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  void _showDeleteDialog(int todoId, String title) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          title: Text('Delete Todo'),
          content: Text('Are you sure you want to delete "$title"?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('Cancel'),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                _deleteTodo(todoId);
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                foregroundColor: Colors.white,
              ),
              child: Text('Delete'),
            ),
          ],
        );
      },
    );
  }

  // Method untuk menampilkan dialog bahwa tugas tidak bisa diedit
  void _showCompletedTaskDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          title: Row(
            children: [
              Icon(Icons.info, color: Colors.blue[600]),
              SizedBox(width: 8),
              Text('Task Completed'),
            ],
          ),
          content: Text(
            'This task is already completed and cannot be edited. You can mark it as pending to edit it again.',
            style: TextStyle(fontSize: 14),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text('OK'),
            ),
          ],
        );
      },
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return Colors.green;
      case 'pending':
        return Colors.orange;
      case 'in_progress':
        return Colors.blue;
      case 'late':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return Icons.check_circle;
      case 'pending':
        return Icons.schedule;
      case 'in_progress':
        return Icons.timelapse;
      case 'late':
        return Icons.warning;
      default:
        return Icons.radio_button_unchecked;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        elevation: 0,
        backgroundColor: Colors.blue[600],
        title: Text(
          'My Todos',
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
        actions: [
          PopupMenuButton<String>(
            icon: Icon(Icons.more_vert, color: Colors.white),
            onSelected: (String value) {
              if (value == 'logout') {
                _logout();
              }
            },
            itemBuilder: (BuildContext context) => [
              PopupMenuItem<String>(
                value: 'logout',
                child: Row(
                  children: [
                    Icon(Icons.logout, color: Colors.red),
                    SizedBox(width: 8),
                    Text('Logout'),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
      body: Column(
        children: [
          // Header with user info
          Container(
            width: double.infinity,
            decoration: BoxDecoration(
              color: Colors.blue[600],
              borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(30),
                bottomRight: Radius.circular(30),
              ),
            ),
            child: Padding(
              padding: EdgeInsets.fromLTRB(24, 0, 24, 30),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Welcome back,',
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 16,
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    _userName,
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 16),
                  Container(
                    padding: EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.task_alt, color: Colors.white),
                        SizedBox(width: 12),
                        Text(
                          '${_todos.length} Total Tasks',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 16,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // Todo List
          Expanded(
            child: _isLoading
                ? Center(
                    child: CircularProgressIndicator(
                      valueColor: AlwaysStoppedAnimation<Color>(Colors.blue[600]!),
                    ),
                  )
                : _todos.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.task_outlined,
                              size: 80,
                              color: Colors.grey[400],
                            ),
                            SizedBox(height: 16),
                            Text(
                              'No todos yet',
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.w500,
                                color: Colors.grey[600],
                              ),
                            ),
                            SizedBox(height: 8),
                            Text(
                              'Add your first todo to get started',
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey[500],
                              ),
                            ),
                          ],
                        ),
                      )
                    : RefreshIndicator(
                        onRefresh: _fetchTodos,
                        child: ListView.builder(
                          padding: EdgeInsets.all(16),
                          itemCount: _todos.length,
                          itemBuilder: (context, index) {
                            final todo = _todos[index];
                            final status = todo['status'].toString().toLowerCase();
                            final isCompleted = status == 'completed';
                            
                            return Container(
                              margin: EdgeInsets.only(bottom: 12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                border: status == 'late' 
                                    ? Border.all(color: Colors.red.withOpacity(0.3), width: 2)
                                    : isCompleted
                                        ? Border.all(color: Colors.green.withOpacity(0.3), width: 2)
                                        : null,
                                boxShadow: [
                                  BoxShadow(
                                    color: status == 'late' 
                                        ? Colors.red.withOpacity(0.1)
                                        : isCompleted
                                            ? Colors.green.withOpacity(0.1)
                                            : Colors.black.withOpacity(0.05),
                                    blurRadius: 10,
                                    offset: Offset(0, 2),
                                  ),
                                ],
                              ),
                              child: ListTile(
                                contentPadding: EdgeInsets.all(16),
                                leading: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    // Checkbox untuk status
                                    GestureDetector(
                                      onTap: () {
                                        if (status == 'pending' || status == 'late') {
                                          _updateStatus(todo['id'], 'completed');
                                        } else if (status == 'completed') {
                                          _updateStatus(todo['id'], 'pending');
                                        }
                                      },
                                      child: Container(
                                        width: 24,
                                        height: 24,
                                        decoration: BoxDecoration(
                                          color: isCompleted 
                                              ? Colors.green 
                                              : Colors.transparent,
                                          border: Border.all(
                                            color: isCompleted 
                                                ? Colors.green 
                                                : _getStatusColor(status),
                                            width: 2,
                                          ),
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: isCompleted
                                            ? Icon(
                                                Icons.check,
                                                size: 16,
                                                color: Colors.white,
                                              )
                                            : null,
                                      ),
                                    ),
                                    SizedBox(width: 12),
                                    // Status icon
                                    Container(
                                      width: 50,
                                      height: 50,
                                      decoration: BoxDecoration(
                                        color: _getStatusColor(status).withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: Icon(
                                        _getStatusIcon(status),
                                        color: _getStatusColor(status),
                                        size: 24,
                                      ),
                                    ),
                                  ],
                                ),
                                title: Text(
                                  todo['title'] ?? 'No Title',
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w600,
                                    color: isCompleted ? Colors.grey[500] : Colors.grey[800],
                                    decoration: isCompleted 
                                        ? TextDecoration.lineThrough 
                                        : TextDecoration.none,
                                    decorationColor: Colors.grey,
                                  ),
                                ),
                                subtitle: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    if (todo['description'] != null && todo['description'].isNotEmpty)
                                      Padding(
                                        padding: EdgeInsets.only(top: 4),
                                        child: Text(
                                          todo['description'],
                                          style: TextStyle(
                                            fontSize: 14,
                                            color: isCompleted ? Colors.grey[400] : Colors.grey[600],
                                            decoration: isCompleted 
                                                ? TextDecoration.lineThrough 
                                                : TextDecoration.none,
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                      ),
                                    SizedBox(height: 8),
                                    Row(
                                      children: [
                                        Icon(
                                          Icons.access_time, 
                                          size: 16, 
                                          color: isCompleted ? Colors.grey[400] : Colors.grey[500],
                                        ),
                                        SizedBox(width: 4),
                                        Text(
                                          todo['deadline'] ?? 'No deadline',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: isCompleted 
                                                ? Colors.grey[400]
                                                : status == 'late' 
                                                    ? Colors.red 
                                                    : Colors.grey[500],
                                            fontWeight: status == 'late' && !isCompleted 
                                                ? FontWeight.bold 
                                                : FontWeight.normal,
                                            decoration: isCompleted 
                                                ? TextDecoration.lineThrough 
                                                : TextDecoration.none,
                                          ),
                                        ),
                                        SizedBox(width: 16),
                                        Container(
                                          padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                          decoration: BoxDecoration(
                                            color: _getStatusColor(status).withOpacity(0.1),
                                            borderRadius: BorderRadius.circular(8),
                                            border: status == 'late' && !isCompleted
                                                ? Border.all(color: Colors.red.withOpacity(0.5))
                                                : isCompleted
                                                    ? Border.all(color: Colors.green.withOpacity(0.5))
                                                    : null,
                                          ),
                                          child: Text(
                                            isCompleted 
                                                ? 'COMPLETED'
                                                : status == 'late' 
                                                    ? 'OVERDUE' 
                                                    : status.toUpperCase(),
                                            style: TextStyle(
                                              fontSize: 10,
                                              fontWeight: FontWeight.w600,
                                              color: _getStatusColor(status),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                                trailing: PopupMenuButton<String>(
                                  onSelected: (String value) {
                                    if (value == 'edit') {
                                      // Cek apakah status completed
                                      if (isCompleted) {
                                        _showCompletedTaskDialog();
                                      } else {
                                        _showEditDialog(todo);
                                      }
                                    } else if (value == 'delete') {
                                      _showDeleteDialog(todo['id'], todo['title']);
                                    } else if (value == 'mark_completed') {
                                      _updateStatus(todo['id'], 'completed');
                                    } else if (value == 'mark_pending') {
                                      _updateStatus(todo['id'], 'pending');
                                    }
                                  },
                                  itemBuilder: (BuildContext context) {
                                    List<PopupMenuEntry<String>> menuItems = [];
                                    
                                    // Status change options
                                    if (!isCompleted) {
                                      menuItems.add(
                                        PopupMenuItem<String>(
                                          value: 'mark_completed',
                                          child: Row(
                                            children: [
                                              Icon(Icons.check_circle, size: 16, color: Colors.green),
                                              SizedBox(width: 8),
                                              Text('Mark as Completed'),
                                            ],
                                          ),
                                        ),
                                      );
                                    }
                                    
                                    if (isCompleted) {
                                      menuItems.add(
                                        PopupMenuItem<String>(
                                          value: 'mark_pending',
                                          child: Row(
                                            children: [
                                              Icon(Icons.schedule, size: 16, color: Colors.orange),
                                              SizedBox(width: 8),
                                              Text('Mark as Pending'),
                                            ],
                                          ),
                                        ),
                                      );
                                    }
                                    
                                    // Edit option - disabled untuk completed tasks
                                    menuItems.add(
                                      PopupMenuItem<String>(
                                        value: 'edit',
                                        enabled: !isCompleted,
                                        child: Row(
                                          children: [
                                            Icon(
                                              Icons.edit, 
                                              size: 16,
                                              color: isCompleted ? Colors.grey[400] : Colors.grey[700],
                                            ),
                                            SizedBox(width: 8),
                                            Text(
                                              'Edit',
                                              style: TextStyle(
                                                color: isCompleted ? Colors.grey[400] : Colors.grey[700],
                                              ),
                                            ),
                                            if (isCompleted) ...[
                                              SizedBox(width: 8),
                                              Icon(
                                                Icons.lock,
                                                size: 12,
                                                color: Colors.grey[400],
                                              ),
                                            ],
                                          ],
                                        ),
                                      ),
                                    );
                                    
                                    // Delete option
                                    menuItems.add(
                                      PopupMenuItem<String>(
                                        value: 'delete',
                                        child: Row(
                                          children: [
                                            Icon(Icons.delete, size: 16, color: Colors.red),
                                            SizedBox(width: 8),
                                            Text('Delete', style: TextStyle(color: Colors.red)),
                                          ],
                                        ),
                                      ),
                                    );
                                    
                                    return menuItems;
                                  },
                                ),
                              ),
                            );
                          },
                        ),
                      ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showAddDialog(),
        backgroundColor: Colors.blue[600],
        child: Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  void _showAddDialog() {
    _showTodoDialog();
  }

  void _showEditDialog(Map<String, dynamic> todo) {
    // Double check - jangan biarkan edit jika completed
    if (todo['status'].toString().toLowerCase() == 'completed') {
      _showCompletedTaskDialog();
      return;
    }
    _showTodoDialog(todo: todo);
  }

  void _showTodoDialog({Map<String, dynamic>? todo}) {
    final _titleController = TextEditingController(text: todo?['title'] ?? '');
    final _descriptionController = TextEditingController(text: todo?['description'] ?? '');
    final _deadlineController = TextEditingController(text: todo?['deadline'] ?? '');
    final _formKey = GlobalKey<FormState>();
    bool _isLoading = false;
    DateTime? _selectedDate;

    // Parse existing date if editing
    if (todo != null && todo['deadline'] != null) {
      try {
        _selectedDate = DateTime.parse(todo['deadline']);
      } catch (e) {
        _selectedDate = null;
      }
    }

    Future<void> _selectDate() async {
      final DateTime now = DateTime.now();
      final DateTime today = DateTime(now.year, now.month, now.day);
      
      final DateTime? picked = await showDatePicker(
        context: context,
        initialDate: _selectedDate ?? today,
        firstDate: today, // Tidak boleh pilih tanggal sebelum hari ini
        lastDate: DateTime(2030),
        helpText: 'Select deadline',
        cancelText: 'Cancel',
        confirmText: 'OK',
        builder: (context, child) {
          return Theme(
            data: Theme.of(context).copyWith(
              colorScheme: ColorScheme.light(
                primary: Colors.blue[600]!,
                onPrimary: Colors.white,
                surface: Colors.white,
                onSurface: Colors.black,
              ),
            ),
            child: child!,
          );
        },
      );

      if (picked != null) {
        setState(() {
          _selectedDate = picked;
          _deadlineController.text = DateFormat('yyyy-MM-dd').format(picked);
        });
      }
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              title: Row(
                children: [
                  Icon(
                    todo == null ? Icons.add_task : Icons.edit,
                    color: Colors.blue[600],
                  ),
                  SizedBox(width: 8),
                  Text(todo == null ? 'Add Todo' : 'Edit Todo'),
                ],
              ),
              content: Form(
                key: _formKey,
                child: SingleChildScrollView(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Title Field
                      TextFormField(
                        controller: _titleController,
                        decoration: InputDecoration(
                          labelText: 'Title *',
                          prefixIcon: Icon(Icons.title, color: Colors.blue[600]),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.blue[600]!),
                          ),
                        ),
                        validator: (value) {
                          if (value == null || value.trim().isEmpty) {
                            return 'Please enter a title';
                          }
                          return null;
                        },
                      ),
                      SizedBox(height: 16),

                      // Description Field
                      TextFormField(
                        controller: _descriptionController,
                        decoration: InputDecoration(
                          labelText: 'Description',
                          prefixIcon: Icon(Icons.description, color: Colors.blue[600]),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.blue[600]!),
                          ),
                        ),
                        maxLines: 3,
                      ),
                      SizedBox(height: 16),

                      // Deadline Field
                      TextFormField(
                        controller: _deadlineController,
                        decoration: InputDecoration(
                          labelText: 'Deadline *',
                          prefixIcon: Icon(Icons.calendar_today, color: Colors.blue[600]),
                          suffixIcon: IconButton(
                            icon: Icon(Icons.date_range, color: Colors.blue[600]),
                            onPressed: _selectDate,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide(color: Colors.blue[600]!),
                          ),
                          hintText: 'Select deadline date',
                        ),
                        readOnly: true,
                        onTap: _selectDate,
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Please select a deadline';
                          }
                          
                          // Validasi tambahan: cek apakah tanggal tidak kurang dari hari ini
                          try {
                            final selectedDate = DateTime.parse(value);
                            final today = DateTime.now();
                            final todayOnly = DateTime(today.year, today.month, today.day);
                            final selectedOnly = DateTime(selectedDate.year, selectedDate.month, selectedDate.day);
                            
                            if (selectedOnly.isBefore(todayOnly)) {
                              return 'Deadline cannot be before today';
                            }
                          } catch (e) {
                            return 'Invalid date format';
                          }
                          
                          return null;
                        },
                      ),
                      
                      if (_selectedDate != null) ...[
                        SizedBox(height: 8),
                        Container(
                          padding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          decoration: BoxDecoration(
                            color: Colors.blue[50],
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.info, size: 16, color: Colors.blue[600]),
                              SizedBox(width: 8),
                              Text(
                                'Selected: ${DateFormat('EEEE, MMM dd, yyyy').format(_selectedDate!)}',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.blue[600],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              actions: [
                TextButton(
                  onPressed: _isLoading ? null : () => Navigator.pop(context),
                  child: Text(
                    'Cancel',
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ),
                ElevatedButton(
                  onPressed: _isLoading
                      ? null
                      : () async {
                          if (_formKey.currentState!.validate()) {
                            setState(() {
                              _isLoading = true;
                            });

                            try {
                              SharedPreferences prefs = await SharedPreferences.getInstance();
                              String? token = prefs.getString('token');

                              final url = todo == null
                                  ? '$base_url/todos'
                                  : '$base_url/todos/${todo['id']}';

                              final method = todo == null ? 'POST' : 'PUT';

                              final response = method == 'POST'
                                  ? await http.post(
                                      Uri.parse(url),
                                      headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'Authorization': 'Bearer $token',
                                      },
                                      body: json.encode({
                                        'title': _titleController.text.trim(),
                                        'description': _descriptionController.text.trim(),
                                        'deadline': _deadlineController.text,
                                      }),
                                    )
                                  : await http.put(
                                      Uri.parse(url),
                                      headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'Authorization': 'Bearer $token',
                                      },
                                      body: json.encode({
                                        'title': _titleController.text.trim(),
                                        'description': _descriptionController.text.trim(),
                                        'deadline': _deadlineController.text,
                                      }),
                                    );

                              if (response.statusCode == 200) {
                                Navigator.pop(context);
                                _showSnackBar(
                                  todo == null ? 'Todo added successfully' : 'Todo updated successfully',
                                  Colors.green,
                                );
                                _fetchTodos();
                              } else {
                                final errorData = json.decode(response.body);
                                _showSnackBar(
                                  errorData['message'] ?? 'Failed to save todo',
                                  Colors.red,
                                );
                              }
                            } catch (e) {
                              _showSnackBar('Network error: $e', Colors.red);
                            } finally {
                              setState(() {
                                _isLoading = false;
                              });
                            }
                          }
                        },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blue[600],
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: _isLoading
                      ? SizedBox(
                          width: 16,
                          height: 16,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : Text(todo == null ? 'Add Todo' : 'Update Todo'),
                ),
              ],
            );
          },
        );
      },
    );
  }
}