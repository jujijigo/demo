## 原生 PHP 项目  
原生 PHP 项目主要体现 PHP 语言和原生函数（图片函数、PDO 类及其方法、SESSION 函数、正则函数、文件系统函数、目录函数等）的应用能力，PHP 操作数据库能力（PDO 扩展、SQL 语法语句，简单查询复杂查询等），基本的前端页面编写，ajax 应用能力等。

项目网址：http://www.anydemo.cn

原生 PHP 项目包括：
1. 验证码类
2. 简易留言板
3. 注册/登录
4. 高级留言板
5. 在线网盘

### 前端
前端页面效果和交互使用的技术有：HTML、CSS、JavaScript、bootstrap、jQuery、ajax、layer。
HTML 和 CSS 实现基本的 HTML 页面，bootstrap 设置页面样式，JavaScript 和 jQuery 主要是实现事件监听和 ajax，layer 实现用户交互的弹出层。

### 后端
- 验证码类：使用 GD 库及相关图片处理函数。实现验证码图片的生成和显示。应用于下面所有项目中。
- 简易留言板：使用 PDO 扩展，PHP 操作 MySQL 数据库相关原生函数。实现留言功能。
- 注册/登录：使用 PDO 扩展，PHP 操作 MySQL 数据库相关原生函数、SESSION 相关函数。实现用户的注册、登录、注销功能。
- 高级留言板：使用 PDO 扩展，PHP 操作 MySQL 数据库相关原生函数、SESSION 相关函数。实现和用户系统结合的留言功能。
高级留言板和简易留言板的区别主要有2点：1、高级留言板结合了用户系统，必须注册且登录才能留言，而简易留言板不需要登录，直接输入留言和一个任意昵称即可发表留言；2、高级留言板使用到了简单的多表联合查询（用户表和留言表），简易留言板仅仅操作一个数据表。
- 在线网盘：使用 PHP 原生文件函数和目录相关函数、SESSION 相关函数。实现与用户系统结合的在线网盘系统。此项目相比前面几个项目应用了较多的 ajax。

## ThinkPHP5 项目
ThinkPHP5 项目主要体现 ThinkPHP5 框架和 MVC 架构模式的理解和应用；基于 ThinkPHP5 模型类的 MySQL 数据库增删改、简单查询复杂查询、关联写入、多表联查、分页、排序等 SQL 语法和数据库的理解应用能力；另外还使用到了 Memcached、ThinkPHP 扩展（captcha）、SESSION 等。实现了一个功能相对完整的 BBS 论坛系统。

ThinkPHP5 项目与原生 PHP 项目的不同：ThinkPHP 项目基于 ThinkPHP 框架采用 MVC 架构模式和面向对象编程方式。

项目网址：http://bbs.anydemo.cn

BBS 功能包括：
1. 用户注册和登录。
2. 帖子发表、编辑、回复、点赞。
3. 帖子列表显示及其排序：
4. 热门标签和关键词搜索。
5. 帖子列表页面和搜索页面的缓存：使用 memcached，减轻 MySQL 数据库压力。
6. 后台管理：实现用户数量、点赞数、回复数、帖子数量的后台统计，提供表现层的帖子删除和用户删除。“后台管理功能”入口在网站页面最下方“管理员登录”链接。