# Documentation Index

Welcome to the Laravel DTO application documentation! This comprehensive guide covers everything you need to know about building, testing, and deploying applications using Domain Driven Design principles with Spatie Laravel Data.

## 📚 Documentation Structure

### 🤖 Auto-Generated Documentation
Real-time documentation updated automatically by CI/CD:
- **Quality Metrics**: Current test results, PHPStan analysis, and ESLint status
- **Command Reference**: Available artisan commands and NPM scripts
- **API Routes**: Complete list of application routes
- **Project Structure**: Current directory structure and organization
- **Recent Changes**: Latest commits and code changes
- **Configuration Examples**: Live configuration files and examples

*Note: Auto-generated docs are created by GitHub Actions and updated on every push to master.*

### 🏗️ [Architecture Documentation](./ARCHITECTURE.md)
Deep dive into the application's architecture, including:
- Domain Driven Design principles
- Clean Architecture implementation
- Layer responsibilities and dependencies
- DTO and Action patterns
- Error handling strategies
- Security considerations
- Performance optimization

### 🚀 [API Documentation](./API.md)
Complete API reference including:
- Authentication endpoints
- User management APIs
- Request/response examples
- DTO definitions
- Validation rules
- Error handling
- Rate limiting
- Testing examples

### 🧪 [Testing Guide](./TESTING.md)
Comprehensive testing strategies covering:
- Unit testing DTOs and Actions
- Integration testing
- Feature testing HTTP endpoints
- Test organization and best practices
- Mocking and stubbing
- Performance testing
- CI/CD integration
- Code quality testing integration

### 🔍 Code Quality & Linting
Code quality standards and tools:
- **PHP Static Analysis**: PHPStan Level 8 with Laravel support
- **JavaScript/TypeScript Linting**: ESLint 9 with Vue.js 3 + TypeScript
- **Code Formatting**: Prettier with automatic formatting
- **Quality Gates**: Pre-commit hooks and CI/CD integration
- **IDE Integration**: VS Code and PhpStorm setup guides
- **Best Practices**: Type safety, null checks, and consistency standards

### 🐳 [Local Development Guide](./LOCAL_DEVELOPMENT.md)
Complete DDEV setup and development workflow:
- **DDEV Installation**: Step-by-step installation for all platforms
- **Quick Start**: Get the application running in minutes
- **Development Shortcuts**: Essential DDEV, Artisan, and NPM commands
- **Custom Commands**: Productivity shortcuts and automation
- **Troubleshooting**: Common issues and solutions
- **Performance Tips**: Optimization and workflow best practices

### 🚢 [Deployment Guide](./DEPLOYMENT.md)
Production-ready deployment instructions:
- Environment setup (dev, staging, production)
- Server configuration
- Deployment strategies
- Database migrations
- Performance optimization
- Monitoring and logging
- Backup strategies
- Security considerations
- Troubleshooting

## 🎯 Quick Navigation

### For Developers New to the Project
1. Start with the main [README.md](../README.md) for project overview
2. Review [Architecture Documentation](./ARCHITECTURE.md) to understand the patterns
3. Follow the Quick Start guide in the main README
4. Explore [API Documentation](./API.md) for endpoint details

### For QA Engineers
1. Read [Testing Guide](./TESTING.md) for testing strategies
2. Review [API Documentation](./API.md) for endpoint testing
3. Check main README for test execution commands

### For DevOps Engineers
1. Study [Deployment Guide](./DEPLOYMENT.md) for infrastructure setup
2. Review [Architecture Documentation](./ARCHITECTURE.md) for system dependencies
3. Check security and monitoring sections

### For Architects and Team Leads
1. Review [Architecture Documentation](./ARCHITECTURE.md) for design decisions
2. Study [Testing Guide](./TESTING.md) for quality assurance strategies
3. Check [Deployment Guide](./DEPLOYMENT.md) for scalability considerations

## 🔧 Key Concepts

### Domain Driven Design (DDD)
This application demonstrates:
- **Domain Layer**: Core business logic with Actions and DTOs
- **Application Layer**: HTTP controllers and infrastructure concerns
- **Support Layer**: Shared utilities and base classes

### Data Transfer Objects (DTOs)
Using Spatie Laravel Data for:
- Type-safe data handling
- Declarative validation
- Request/response transformation
- API contract definition

### Actions Pattern
Business logic encapsulated in:
- Single-responsibility classes
- Testable, composable operations
- Dependency injection support
- Clear input/output contracts

## 📖 Learning Path

### Beginner (New to DDD/DTOs)
1. **Read**: Main README overview
2. **Study**: Architecture basics in [ARCHITECTURE.md](./ARCHITECTURE.md)
3. **Practice**: Run the quick start example
4. **Explore**: Simple DTO and Action examples

### Intermediate (Familiar with Laravel)
1. **Understand**: Complete architecture in [ARCHITECTURE.md](./ARCHITECTURE.md)
2. **Learn**: Testing patterns in [TESTING.md](./TESTING.md)
3. **Practice**: Write your own DTOs and Actions
4. **Test**: API endpoints using [API.md](./API.md)

### Advanced (Ready for Production)
1. **Master**: All architecture patterns
2. **Implement**: Comprehensive testing strategies
3. **Deploy**: Using [DEPLOYMENT.md](./DEPLOYMENT.md) guide
4. **Monitor**: Production applications

## 🛠️ Code Examples

All documentation includes practical examples:

### DTO Example
```php
class ProfileUpdateData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        
        #[Required, Email, Max(255)]
        public string $email,
    ) {}
}
```

### Action Example
```php
class UpdateUserProfileAction
{
    public function __invoke(User $user, ProfileUpdateData $data): User
    {
        $user->fill($data->toArray());
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        return $user;
    }
}
```

### Controller Example
```php
public function update(Request $request): RedirectResponse
{
    // 1. Transform request to DTO (automatic validation)
    $profileData = ProfileUpdateData::from($request->all());
    
    // 2. Execute business logic via Action
    app(UpdateUserProfileAction::class)(auth()->user(), $profileData);
    
    // 3. Return response
    return to_route('profile.edit');
}
```

## 🔍 Search and Navigation Tips

### Finding Specific Information
- **Architecture patterns**: Check [ARCHITECTURE.md](./ARCHITECTURE.md)
- **API endpoints**: Reference [API.md](./API.md)
- **Testing examples**: Browse [TESTING.md](./TESTING.md)
- **Deployment commands**: Look in [DEPLOYMENT.md](./DEPLOYMENT.md)

### Cross-References
Each document links to related sections in other documents. Look for:
- "See also" sections
- Inline links to other documents
- Code examples that reference multiple concepts

## 🤝 Contributing to Documentation

### Documentation Standards
- Use clear, concise language
- Include practical code examples
- Provide both basic and advanced examples
- Link between related concepts
- Keep examples up-to-date with code

### Updating Documentation
When making code changes:
1. Update relevant documentation sections
2. Add new examples if introducing new patterns
3. Update API documentation for endpoint changes
4. Review all cross-references

## 📝 Additional Resources

### External References
- [Spatie Laravel Data Documentation](https://spatie.be/docs/laravel-data)
- [Domain Driven Design Principles](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [Laravel Documentation](https://laravel.com/docs)
- [Pest PHP Testing Framework](https://pestphp.com/)

### Community
- Share your DDD implementations
- Contribute to Spatie Laravel Data
- Join Laravel community discussions
- Follow DDD best practices

---

**Happy coding!** 🎉

This documentation is living and evolving. If you find areas for improvement or have questions, please contribute back to make it even better for the next developer. 