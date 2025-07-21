<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Laravel Event Management System - Backend API

## Project Context
This is a Laravel backend API for an event management system that serves a separate frontend. The system handles role-based authentication and event management with the following user roles:

- **Client (rol_id: 1)**: Can create and manage their own events
- **Employee (rol_id: 2)**: Can view assigned events and update event status
- **Manager (rol_id: 3)**: Has full access to all system functionality

## Database Schema
The system uses the following main entities:
- **Users**: Authentication and role management
- **Services**: Available event services with pricing
- **Events**: Event bookings with client, service, and employee relationships
- **Seguimientos**: Event tracking/follow-up records

## API Guidelines
- Use Laravel Sanctum for API authentication
- Follow RESTful conventions for API endpoints
- Return consistent JSON responses with proper HTTP status codes
- Implement proper validation for all inputs
- Use Laravel's built-in features for database relationships
- Ensure proper CORS configuration for frontend integration

## Security Requirements
- Implement role-based access control
- Validate user permissions for each endpoint
- Sanitize all input data
- Use Laravel's built-in security features

## Code Style
- Follow Laravel coding standards
- Use Eloquent ORM for database operations
- Implement proper error handling
- Use Laravel's validation system
- Follow dependency injection patterns
