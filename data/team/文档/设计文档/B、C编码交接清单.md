# B、C编码交接清单

本轮数据库和接口设计已由组长确认，11张业务表和119条样本已在组长本机实际落库。以下文件为开发责任和待办，不代表组员代码已编写。路径均相对于仓库data/team/advanced，人员暂称A、B、C。

## 1. 开工先读与当前状态

1. 文档/需求文档/需求文档.md：功能范围。
2. 文档/设计文档/数据库设计暂定基线.md：12表、84字段、可空性、唯一键、索引及业务约束。
3. 文档/设计文档/跨模块接口约定.md：路由、参数、JSON、留言POST及权限。
4. 仓库外“大作业/数据库初始化准备包/00_准备与执行说明.md”：本地SQL和转换材料，不随Git仓库提交；需要时由组长另行提供。原包“未执行”是生成时状态，本机实际结果以建表、导入执行记录为准。

已经具备：当前Git副本可运行；北京时间；唯一启用admin认证、退出和改密；11业务表、119条样本；留言最小骨架。尚未具备：球队/球员/比赛前后台页面、完整业务模型、统一统计服务、图表/对比、最终留言详情POST及后台审核。

所有普通业务记录录入即公开，无发布字段；来源只在文档；比赛日期只有北京时间DATE；comment必须审核。页面注明“精选球员/已收录比赛”，所有统计仅代表本站样本，不代表完整赛事。资料事实本轮未重新核验。

## 2. B：基础资料模块

| 顺序 | 文件/目录（待新增或完善） | 工作及验收出口 |
| --- | --- | --- |
| B1 | common/models/Team.php、Player.php、TeamPlayer.php、Coach.php、TeamCoach.php、Honor.php、TeamHonor.php | 7表模型、rules及关联；表名使用Yii前缀约定；关系查询不以姓名代替ID |
| B2 | frontend/models/TeamSearch.php、PlayerSearch.php | GET筛选、每页10条、ID升序；球员按2026阵容过滤球队与位置 |
| B3 | frontend/controllers/TeamController.php、PlayerController.php、CoachController.php | 球队/球员列表与详情，教练详情可从球队进入；非法/缺失详情ID为404 |
| B4 | frontend/views/team/index.php、view.php；player/index.php、view.php；coach/view.php | 展示资料、阵容、教练、荣誉；缺图占位，未知生日不显示年龄，安全转义 |
| B5 | backend/controllers/TeamController.php、PlayerController.php、TeamPlayerController.php、CoachController.php、TeamCoachController.php、HonorController.php、TeamHonorController.php | 继承AdminController，各自CRUD与依赖删除限制；校验失败保留输入 |
| B6 | backend/views/{team,player,team-player,coach,team-coach,honor,team-honor}/ | 各目录按index/view/create/update/_form组织，可复用表单；不为仅有外键关系增加多余复杂流程 |
| B7 | 前台team/view、player/view及其视图 | 调用A的统一留言处理器和组件；失败422原详情显示、成功303；A处理器未交付时先完成GET页面 |
| B8 | team/view调用C的TeamStatsService；TeamController集成C提供的compare/stats动作 | 只调用统一服务，不复制统计SQL；与C约定合并文件，避免相互覆盖 |

B核心规则：同球员同届仅一队；同队同届号码1—26唯一；位置GK/DF/MF/FW；同队同届仅一名主教练。已有统计的阵容不得修改球队/球员/年份。有关联（包括软删除留言引用）的资料不能直接删除。媒体上传校验沿用需求，具体类型/大小及路径尚未固定，编码前由A/B/C协调一次，不各自采用不兼容配置。

B先完成B1—B4即可为其他模块提供基础，不必等待图表或留言完成。设计/实现文档仍按原分工由B汇总，但数据库先行设计贡献应如实记为组长完成，B记录本人实际模型、校验和修改贡献。

## 3. C：比赛、对比、统计模块

| 顺序 | 文件/目录（待新增或完善） | 工作及验收出口 |
| --- | --- | --- |
| C1 | common/models/Venue.php、FootballMatch.php、PlayerMatchStat.php | 3表模型与关联，NULL比分、库外名称及跨表校验 |
| C2 | common/services/TeamStatsService.php | getSummary($teamId,$year=2026)，统一计算8个整数字段 |
| C3 | frontend/models/FootballMatchSearch.php；frontend/controllers/FootballMatchController.php | GET筛选、每页10条，日期DESC/id DESC；详情支持后续留言POST |
| C4 | frontend/views/football-match/index.php、view.php | 比赛资料、状态/比分；库外球队只显示名称，不生成球队详情链接 |
| C5 | backend/controllers/VenueController.php、FootballMatchController.php、PlayerMatchStatController.php及各自backend/views目录 | 继承AdminController的CRUD，事务及锁定规则、依赖错误提示 |
| C6 | frontend/views/team/compare.php；frontend/web/js/team-stats.js | 对比页及图表交互，C提供文件，B合并TeamController动作；脚本通过项目资产机制注册 |
| C7 | /team/compare与/team/stats动作实现交付B集成 | 对比参数team_a_id/team_b_id；统计JSON接口及统一错误格式，C提供完整依赖清单 |
| C8 | 比赛详情接入A的留言处理器；部署文档/脚本 | 不重复实现留言保存；先完成GET详情，处理器就绪后接入 |

C核心规则：仅finished允许完整非负比分及球员统计；其他状态双方比分NULL。任意统计存在即锁定比赛双方ID/名称、年份、状态、比分，不能自动删除统计解锁。统计阵容年份匹配，球队属于该场，进球不超本队比分；未出场进球为0，未知记录不默认0。外键/行内CHECK不能代替跨表和并发事务校验。

比分含加时、不含点球大战；点球决胜平局仍计平。不要将球员统计表联接到战绩聚合导致一场重复计数。统计JSON数据8字段固定：team_id、tournament_year、played、wins、draws、losses、goals_for、goals_against。字段为整数，played=wins+draws+losses；错误参数400，不存在球队404，服务错误500且不返回SQL/堆栈。

图表切换球队使用GET JSON局部刷新；零场空状态；失败提示重试，忽略过期请求。绘图库具体依赖尚未固定，由C在不改变接口前提下选型并记录，不将某个未经安装的库写为已具备。用户手册和部署材料仍由C汇总。

## 4. A向B、C提供的公共部分

| 文件 | 当前状态 | B、C使用办法 |
| --- | --- | --- |
| backend/controllers/AdminController.php | 已实现 | 继承并保留parent::behaviors；仅@登录规则不能替代指定管理员校验 |
| backend/components/AdminAccess.php | 已实现 | 唯一启用用户名admin（配置可改），不用硬编码ID=1 |
| common/models/User.php | 复用 | 不复制密码逻辑，不重建用户表，不开放前台注册 |
| common/models/Comment.php、frontend/models/CommentForm.php | 已有骨架 | 不由B/C直接保存审核字段 |
| frontend/widgets/CommentWidget.php及frontend/views/comment/ | 已有旧骨架 | 最终调用targetType、targetId、model；表单action尚待A改成详情路由 |
| frontend/components/CommentTarget.php及frontend/config/params.php | 已有适配但配置为空 | 最终详情路由已定，等对应模型/页面及A改造就绪后启用真实exists回调 |
| frontend/components/CommentSubmission.php | 未实现，A下一阶段提供 | submit($type,$id,CommentForm $form)，成功true、失败false并添加错误；A统一保存pending |
| backend/controllers/CommentController.php及审核视图 | 未实现，A负责 | B/C不用重复实现 |

旧/comment/create不是最终正式提交入口，A后续关闭为404；不能继续依赖旧失败结果页。B/C详情GET先独立可运行，待A统一处理器提供后接入POST。不得在Widget::run中执行保存或重定向。

## 5. 固定路由及参数速查

| 功能 | 前台应用内部路由 | 参数/方法 |
| --- | --- | --- |
| 球队列表 | /team/index | GET name、page |
| 球员列表 | /player/index | GET name、team_id、position、page |
| 比赛列表 | /football-match/index | GET team_id、date、stage、status、page |
| 球队/球员/比赛详情 | /team/view、/player/view、/football-match/view | GET id；POST只处理该目标留言 |
| 对比 | /team/compare | GET team_a_id、team_b_id，不可同队 |
| 战绩JSON | /team/stats | GET team_id，公开只读 |

继续Yii默认URL：index.php?r=team%2Fview&id=1。用Url/Html助手生成，不写死本机端口。后台index/view仅GET，create/update GET+POST，delete/review仅POST且CSRF有效。留言分页用comment-page，其余列表用page。

## 6. 本地样本ID（来自已导入准备数据）

ID只是样本数据库主键，不能将恰好相同的球员ID/阵容ID或比赛ID/官方编号视为永久规则。正式代码通过关联字段查询。

### 球队、教练及球员范围

| 球队ID | 球队 | 教练ID及名称 | 球员ID | 本届阵容ID |
| --- | --- | --- | --- | --- |
| 1 | 阿根廷 | 1 利昂内尔·斯卡洛尼 | 1, 2, 3, 4 | 1, 2, 3, 4 |
| 2 | 法国 | 2 迪迪埃·德尚 | 5, 6, 7, 8 | 5, 6, 7, 8 |
| 3 | 西班牙 | 3 路易斯·德拉富恩特 | 9, 10, 11, 12 | 9, 10, 11, 12 |
| 4 | 巴西 | 4 卡洛·安切洛蒂 | 13, 14, 15, 16 | 13, 14, 15, 16 |
| 5 | 葡萄牙 | 5 罗伯托·马丁内斯 | 17, 18, 19, 20 | 17, 18, 19, 20 |
| 6 | 英格兰 | 6 托马斯·图赫尔 | 21, 22, 23, 24 | 21, 22, 23, 24 |

### 球员详情入口样本

| 球员ID | 姓名 | 阵容ID | 球队ID |
| --- | --- | --- | --- |
| 1 | 埃米利亚诺·马丁内斯 | 1 | 1 |
| 2 | 克里斯蒂安·罗梅罗 | 2 | 1 |
| 3 | 恩佐·费尔南德斯 | 3 | 1 |
| 4 | 利昂内尔·梅西 | 4 | 1 |
| 5 | 迈克·迈尼昂 | 5 | 2 |
| 6 | 朱尔·孔德 | 6 | 2 |
| 7 | 奥雷利安·楚阿梅尼 | 7 | 2 |
| 8 | 基利安·姆巴佩 | 8 | 2 |
| 9 | 乌奈·西蒙 | 9 | 3 |
| 10 | 保·库巴西 | 10 | 3 |
| 11 | 佩德里 | 11 | 3 |
| 12 | 拉明·亚马尔 | 12 | 3 |
| 13 | 阿利松·贝克尔 | 13 | 4 |
| 14 | 马尔基尼奥斯 | 14 | 4 |
| 15 | 布鲁诺·吉马良斯 | 15 | 4 |
| 16 | 维尼修斯·儒尼奥尔 | 16 | 4 |
| 17 | 迪奥戈·科斯塔 | 17 | 5 |
| 18 | 鲁本·迪亚斯 | 18 | 5 |
| 19 | 布鲁诺·费尔南德斯 | 19 | 5 |
| 20 | 克里斯蒂亚诺·罗纳尔多 | 20 | 5 |
| 21 | 乔丹·皮克福德 | 21 | 6 |
| 22 | 约翰·斯通斯 | 22 | 6 |
| 23 | 裘德·贝林厄姆 | 23 | 6 |
| 24 | 哈里·凯恩 | 24 | 6 |

### 比赛样本

日期和名称为本地样本内容，来源说明另存，本轮未重新核实事实。

| 比赛主键ID | 官方编号 | 主队→客队 | 球队外键（空为库外） | 北京日期 |
| --- | --- | --- | --- | --- |
| 7 | 7 | 巴西 → 摩洛哥 | 4 / NULL | 2026-06-14 |
| 76 | 76 | 巴西 → 日本 | 4 / NULL | 2026-06-30 |
| 91 | 91 | 巴西 → 挪威 | 4 / NULL | 2026-07-06 |
| 93 | 93 | 葡萄牙 → 西班牙 | 5 / 3 | 2026-07-07 |
| 101 | 101 | 法国 → 西班牙 | 2 / 3 | 2026-07-15 |
| 102 | 102 | 英格兰 → 阿根廷 | 6 / 1 | 2026-07-16 |
| 103 | 103 | 法国 → 英格兰 | 2 / 6 | 2026-07-19 |
| 104 | 104 | 西班牙 → 阿根廷 | 3 / 1 | 2026-07-20 |

场馆样本：1=纽约新泽西体育场；2=休斯敦体育场；3=达拉斯体育场；4=亚特兰大体育场；5=迈阿密体育场。荣誉主键为1,2；留言当前0条。


## 7. 各自电脑运行的边界

组长本机已有数据库不等于组员电脑已有数据库。组员取得同一版本代码后，配置本人PHP/MySQL环境、运行Yii Development初始化并填写本地连接配置；新空库先通过既有Yii初始迁移创建user，再执行新版11表DDL及样本SQL，再按需要在本地交互创建管理员。具体执行由本人检查并确认，不把组长localhost当作远程共享库。

当前组长库已有业务表和样本，不能重跑建表/样本脚本。SQL准备包不是自动幂等安装器。真实密码、本地main-local.php及本地数据库备份不纳入公开提交，不复制组长的含账号备份给组员作为默认部署方案。最终data/install.sql尚待部署阶段汇总。

## 8. 共享文件与协调规则

- frontend/controllers/TeamController.php由B维护主文件，C提供compare/stats动作及相关视图/服务后由B合并；双方不能独立覆盖整文件。
- 公共布局及前台总导航、commentTargets配置由A汇总；B/C提供需要的菜单/回调及调用点。前台首页、仓库链接仍待后续整合，不由每人重复改导航。
- B提供team/player/coach模型；C使用这些类。若C先开工，先完成Venue/FootballMatch/统计服务，关联表SQL已具备，可用Yii Query访问既有表，不自建重复Team模型。
- 模型/公共接口变更先说明受影响方，不把审核会签作为常规开工前置。遵循用户最新安排，最终按真实代码、设计及文档贡献记录。
- 各人实际编写MVC各至少一文件，提交时按本人真实身份保留Git贡献；注释头须补真实姓名/学号，不能把工具生成或集成记作他人已完成贡献。

## 9. 交付给组长的最小证据

B：球队/球员GET页面可运行；本人MVC文件与提交；筛选/分页；阵容/教练唯一约束；权限及依赖删除；缺图/生日空值；提供视图截图及设计/实现文档模块说明。

C：比赛GET页面和后台可运行；统计服务与JSON结构；零场/库外对手/点球平局；有统计比赛字段锁定；对比与图表快速切换；本人MVC文件与提交；提供用户/部署说明。

A：统一提交处理器和组件改造、留言后台审核、全站仓库入口、集成及进度记录。跨模块联调需真实验证，不能仅以文件存在或SQL成功当作页面完成。

尚未约定的实现细节（媒体上传限制、绘图库、首页最终布局）记录为待协调，不阻塞核心模型、查询和GET详情先行开发。本清单仅本地文件交接，没有向组员发送消息或推送远程仓库。
