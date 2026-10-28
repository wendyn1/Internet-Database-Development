from pathlib import Path
from datetime import datetime, timedelta
import json, sqlite3, re, hashlib

OUT = Path(__file__).parent / '球队资料与数据库准备包'
OUT.mkdir(exist_ok=True)
SQUAD = 'https://fdp.fifa.org/assetspublic/ce281/pdf/SquadLists-English.pdf'
SCHEDULE = 'https://www.fifa.com/en/tournaments/mens/worldcup/canadamexicousa2026/articles/match-schedule-fixtures-results-teams-stadiums'
SOURCES = [
 ('S01','FIFA官方2026世界杯名单','球员姓名、生日、位置、号码；赛事期间教练及国籍。PDF第2、7、18、19、35、41页。',SQUAD,'完整PDF文本；号码按名单行序并结合比赛名单核对'),
 ('S02','FIFA官方赛程与赛果','精选比赛的阶段、对阵、比分、场地；索引部分日期存在与足协页面不同的情况，具体日期以逐场来源为准。',SCHEDULE,'搜索索引；官网正文未返回文本'),
 ('S03','FIFA巴西对摩洛哥赛后统计','M7：当地日期、开球时间、比分、巴西所选4名球员出场与进球。','https://www.fifatrainingcentre.com/media/native/tournaments/fifa-world-cup/2026/PMSR-M07-BRA-V-MAR.pdf','完整PDF文本'),
 ('S04','FIFA巴西对摩洛哥比赛报告','M7：比分及名单交叉核对。','https://fdp.fifa.org/assetspublic/ce281/r12465/pdf/FullTimeMatchReport-English.pdf','完整PDF文本'),
 ('S05','巴西足协：巴西2比1日本','M76：当地2026-06-29，比分与比赛场地。','https://www.cbf.com.br/selecao-brasileira/noticias/selecao-masculina/a/de-virada-brasil-derrota-japao-por-2-a-1-e-avanca-as-oitavas-da-copa-do-mundo','搜索结果正文摘要'),
 ('S06','日本足协：M76','M76：当地2026-06-29 12:00，Houston Stadium。','https://www.jfa.jp/eng/samuraiblue/worldcup_2026/final/match_page/m76.html','搜索结果正文摘要'),
 ('S07','巴西足协：巴西1比2挪威','M91：当地日期、比分、纽约新泽西场地。','https://www.cbf.com.br/selecao-brasileira/noticias/selecao-masculina/a/brasil-e-superado-pela-noruega-em-nova-jersey','搜索结果正文摘要'),
 ('S08','挪威足协世界杯媒体指南','M91：2026-07-05 16:00美国当地时间。','https://www.fotball.no/landslag/norge-a-herrer/vm-2026','搜索结果正文摘要'),
 ('S09','西班牙足协：葡萄牙对西班牙赛前信息','M93：当地2026-07-06 14:00，Dallas Stadium。','https://rfef.es/en/noticias/Portugal-and-Spain-Renew-the-Iberian-Derby-in-Dallas','搜索结果正文摘要'),
 ('S10','FBref：葡萄牙对西班牙','M93：0比1；本包4名葡萄牙球员均首发，进球为西班牙球员Merino。','https://fbref.com/en/matches/fe9daeb3/Portugal-Spain-July-6-2026-World-Cup','搜索结果含首发名单；直开403，未声称读取完整正文'),
 ('S11','FIFA：法国对西班牙赛前信息','M101：当地2026-07-14 14:00，Dallas Stadium。','https://www.fifa.com/es/tournaments/mens/worldcup/canadamexicousa2026/articles/francia-espana-copa-mundial-2026-cuando-es-como-ver-en-vivo-entradas-donde','搜索结果正文摘要'),
 ('S12','巴黎圣日耳曼官方：法国0比2西班牙','M101：结果、场地、首发及换人；本包4名法国球员均出场且0进球。','https://www.psg.fr/content/fin-de-parcours-pour-les-bleus-lespagne-de-fabian-ruiz-en-finale-coupe-du-monde-fifa-2026-paris-saint-germain','完整正文及阵容'),
 ('S13','英格兰足协：英格兰1比2阿根廷','M102：当地2026-07-15，20:00 BST；双方首发、结果和全部进球。URL含quarter-final但正文确认半决赛。','https://www.englandfootball.com/england/mens-senior-team/fixtures-results/2025-26/World-Cup/england-argentina-fifa-world-cup-quarter-final-wednesday-15-july-2026-match-centre','完整正文及阵容'),
 ('S14','英格兰足协：法国4比6英格兰','M103：当地2026-07-18，22:00 BST，Hard Rock Stadium。','https://www.englandfootball.com/england/mens-senior-team/fixtures-results/2025-26/World-Cup/france-england-fifa-world-cup-bronze-medal-match-saturday-18-july-2026-match-centre','搜索结果正文摘要'),
 ('S15','FIFA：2026世界杯决赛指南','M104：当地2026-07-19，New York New Jersey Stadium；15:00当地时间见FIFA赛程集锦说明。','https://www.fifa.com/en/articles/final-live-watch-teams-tickets','搜索结果及赛程集锦说明'),
 ('S16','LALIGA官方：西班牙对阿根廷','M104：结果1比0、加时；西班牙本包4人出场均0进球，含Pedri替补登场。','https://www.laliga.com/en-ES/match/temporada-2025-2026-mundial-fifa-canada-mexico-usa-2026-por-determinar-por-determinar-9','完整正文、事件及阵容'),
 ('S17','FIFA：历届世界杯夺冠球队','世界杯冠军年份记录。','https://www.fifa.com/en/tournaments/mens/worldcup/articles/teams-most-wins-titles-trophies','搜索结果摘要，交叉参考S18与S20'),
 ('S18','FIFA：1982至2026世界杯冠军','1986至2026年份冠军信息。','https://www.fifa.com/en/tournaments/mens/worldcup/articles/world-cup-champions-1982-2026-italy-argentina-germany-brazil-france-spain','搜索结果摘要'),
 ('S19','UEFA：葡萄牙2016欧洲杯夺冠','葡萄牙2016欧洲杯冠军。','https://www.uefa.com/uefaeuro/history/news/0253-0d81c3763c88-e741506b5589-1000/','搜索结果含正文'),
 ('S20','阿根廷足协：世界杯三冠','阿根廷1978、1986、2022冠军。','https://www.afa.com.ar/4503/posts/argentina-ostenta-tres-titulos-en-la-historia-de-la-copa-del-mundo','搜索结果正文摘要'),
 ('S21','FIFA：西班牙2026夺冠','西班牙2026冠军，与决赛赛果交叉核对。','https://inside.fifa.com/organisation/news/spain-crowned-world-cup-2026-champions-new-york-new-jersey','搜索结果正文摘要'),
]
source_rows=[dict(source_id=i,title=t,scope=s,url=u,access_note=a,accessed_on='2026-10-28') for i,t,s,u,a in SOURCES]

def src(*ids): return ';'.join(ids)
teams=[]
team_specs=[
 (1,'阿根廷','Argentina','ARG','阿根廷','南美洲','传统世界杯冠军球队，适合展示球员资料与冠军荣誉。','梅西、恩佐；可与英格兰和西班牙关联展示。',2),
 (2,'法国','France','FRA','法国','欧洲','拥有世界杯冠军历史，适合展示球员与比赛关联。','姆巴佩；与西班牙、英格兰均有本包比赛。',19),
 (3,'西班牙','Spain','ESP','西班牙','欧洲','以国家队阵容、比赛与历史荣誉为核心展示内容。','亚马尔、佩德里；可串联葡萄牙、法国和阿根廷。',41),
 (4,'巴西','Brazil','BRA','巴西','南美洲','具有丰富世界杯冠军记录，适合荣誉与战绩图表。','维尼修斯；样本包含胜、平、负，适合验证饼图。',7),
 (5,'葡萄牙','Portugal','POR','葡萄牙','欧洲','展示球队阵容、球星资料与欧洲杯冠军荣誉。','C罗、布鲁诺；与西班牙构成直接对比。',35),
 (6,'英格兰','England','ENG','英格兰','欧洲','具有世界杯冠军历史，适合球员资料和比赛展示。','凯恩、贝林厄姆；与阿根廷、法国均有本包比赛。',18),
]
for i,zh,en,code,country,continent,intro,reason,page in team_specs:
 teams.append(dict(id=i,name_zh=zh,name_en=en,fifa_code=code,country=country,continent=continent,introduction=intro,logo_path=None,source_ids='S01',source_page=page))

# 每队4人，覆盖GK/DF/MF/FW；姓名中文为展示译名，英文使用常用名。
player_specs=[
 (1,'埃米利亚诺·马丁内斯','Emiliano Martínez','1992-09-02',23,'GK'),
 (1,'克里斯蒂安·罗梅罗','Cristian Romero','1998-04-27',13,'DF'),
 (1,'恩佐·费尔南德斯','Enzo Fernández','2001-01-17',24,'MF'),
 (1,'利昂内尔·梅西','Lionel Messi','1987-06-24',10,'FW'),
 (2,'迈克·迈尼昂','Mike Maignan','1995-07-03',16,'GK'),
 (2,'朱尔·孔德','Jules Koundé','1998-11-12',5,'DF'),
 (2,'奥雷利安·楚阿梅尼','Aurélien Tchouaméni','2000-01-27',8,'MF'),
 (2,'基利安·姆巴佩','Kylian Mbappé','1998-12-20',10,'FW'),
 (3,'乌奈·西蒙','Unai Simón','1997-06-11',23,'GK'),
 (3,'保·库巴西','Pau Cubarsí','2007-01-22',22,'DF'),
 (3,'佩德里','Pedri','2002-11-25',20,'MF'),
 (3,'拉明·亚马尔','Lamine Yamal','2007-07-13',19,'FW'),
 (4,'阿利松·贝克尔','Alisson Becker','1992-10-02',1,'GK'),
 (4,'马尔基尼奥斯','Marquinhos','1994-05-14',4,'DF'),
 (4,'布鲁诺·吉马良斯','Bruno Guimarães','1997-11-16',8,'MF'),
 (4,'维尼修斯·儒尼奥尔','Vinícius Júnior','2000-07-12',7,'FW'),
 (5,'迪奥戈·科斯塔','Diogo Costa','1999-09-19',1,'GK'),
 (5,'鲁本·迪亚斯','Rúben Dias','1997-05-14',3,'DF'),
 (5,'布鲁诺·费尔南德斯','Bruno Fernandes','1994-09-08',8,'MF'),
 (5,'克里斯蒂亚诺·罗纳尔多','Cristiano Ronaldo','1985-02-05',7,'FW'),
 (6,'乔丹·皮克福德','Jordan Pickford','1994-03-07',1,'GK'),
 (6,'约翰·斯通斯','John Stones','1994-05-28',5,'DF'),
 (6,'裘德·贝林厄姆','Jude Bellingham','2003-06-29',10,'MF'),
 (6,'哈里·凯恩','Harry Kane','1993-07-28',9,'FW'),
]
players=[]; roster=[]
for pid,(tid,zh,en,dob,no,pos) in enumerate(player_specs,1):
 players.append(dict(id=pid,name_zh=zh,name_en=en,nationality=teams[tid-1]['country'],birth_date=dob,photo_path=None,source_ids='S01',source_page=teams[tid-1]['source_page']))
 roster.append(dict(id=pid,team_id=tid,player_id=pid,tournament_year=2026,shirt_number=no,position_code=pos,source_ids='S01'))

coaches=[]; tc=[]
for cid,(zh,en,nat) in enumerate([
 ('利昂内尔·斯卡洛尼','Lionel Scaloni','阿根廷'),('迪迪埃·德尚','Didier Deschamps','法国'),('路易斯·德拉富恩特','Luis de la Fuente','西班牙'),('卡洛·安切洛蒂','Carlo Ancelotti','意大利'),('罗伯托·马丁内斯','Roberto Martínez','西班牙'),('托马斯·图赫尔','Thomas Tuchel','德国')],1):
 coaches.append(dict(id=cid,name_zh=zh,name_en=en,nationality=nat,introduction=f'2026世界杯期间{teams[cid-1]["name_zh"]}国家队主教练；本记录不代表2026年10月现任情况。',photo_path=None,source_ids='S01',source_page=teams[cid-1]['source_page']))
 tc.append(dict(id=cid,team_id=cid,coach_id=cid,tournament_year=2026,role='head_coach',source_ids='S01'))

venues=[
 dict(id=1,name_zh='纽约新泽西体育场',name_en='New York New Jersey Stadium',city='East Rutherford',country='美国',timezone='America/New_York',source_ids='S03;S08;S15'),
 dict(id=2,name_zh='休斯敦体育场',name_en='Houston Stadium',city='Houston',country='美国',timezone='America/Chicago',source_ids='S05;S06'),
 dict(id=3,name_zh='达拉斯体育场',name_en='Dallas Stadium',city='Arlington',country='美国',timezone='America/Chicago',source_ids='S09;S11;S12'),
 dict(id=4,name_zh='亚特兰大体育场',name_en='Atlanta Stadium',city='Atlanta',country='美国',timezone='America/New_York',source_ids='S13'),
 dict(id=5,name_zh='迈阿密体育场',name_en='Miami Stadium',city='Miami Gardens',country='美国',timezone='America/New_York',source_ids='S14'),
]
matches=[]
match_specs=[
 (7,'group','2026-06-13 18:00:00',-4,4,None,'摩洛哥',1,1,1,False,'S03;S04'),
 (76,'round_of_32','2026-06-29 12:00:00',-5,4,None,'日本',2,1,2,False,'S05;S06'),
 (91,'round_of_16','2026-07-05 16:00:00',-4,4,None,'挪威',1,2,1,False,'S07;S08'),
 (93,'round_of_16','2026-07-06 14:00:00',-5,5,3,None,0,1,3,False,'S09;S10;S02'),
 (101,'semi_final','2026-07-14 14:00:00',-5,2,3,None,0,2,3,False,'S11;S12'),
 (102,'semi_final','2026-07-15 15:00:00',-4,6,1,None,1,2,4,False,'S13'),
 (103,'third_place','2026-07-18 17:00:00',-4,2,6,None,4,6,5,False,'S14;S02'),
 (104,'final','2026-07-19 15:00:00',-4,3,1,None,1,0,1,True,'S15;S16;S02'),
]
for mid,stage,local,offset,home,away,external,hg,ag,vid,et,sources in match_specs:
 dt=datetime.fromisoformat(local); utc=dt-timedelta(hours=offset); bj=utc+timedelta(hours=8)
 matches.append(dict(id=mid,fifa_match_number=mid,tournament_year=2026,stage=stage,venue_id=vid,home_team_id=home,away_team_id=away,home_team_name=teams[home-1]['name_zh'],away_team_name=teams[away-1]['name_zh'] if away else external,local_match_date=str(dt.date()),kickoff_utc=utc.isoformat(sep=' '),match_date_beijing=str(bj.date()),status='finished',home_score=hg,away_score=ag,after_extra_time=int(et),source_ids=sources))

stats=[]
# 每位选定球员各收录一场已核实比赛；不是其整届赛事总计。
for p in players:
 pid=p['id']; tid=roster[pid-1]['team_id']
 mid={1:102,2:101,3:104,4:7,5:93,6:102}[tid]
 sources={1:'S13',2:'S12',3:'S16',4:'S03;S04',5:'S10',6:'S13'}[tid]
 goals=1 if pid in (3,16) else 0
 stats.append(dict(id=pid,match_id=mid,team_player_id=pid,appeared=1,goals=goals,assists=None,source_ids=sources))

honors=[dict(id=1,name='世界杯冠军',competition='FIFA World Cup',source_ids='S17;S18'),dict(id=2,name='欧洲杯冠军',competition='UEFA EURO',source_ids='S19')]
th=[]
for tid,years in [(1,[1978,1986,2022]),(2,[1998,2018]),(3,[2010,2026]),(4,[1958,1962,1970,1994,2002]),(6,[1966])]:
 for y in years:
  sources='S20' if tid==1 else ('S21' if tid==3 and y==2026 else 'S17;S18')
  th.append(dict(id=len(th)+1,team_id=tid,honor_id=1,year=y,description=f'{y}年世界杯冠军',source_ids=sources))
th.append(dict(id=len(th)+1,team_id=5,honor_id=2,year=2016,description='2016年欧洲杯冠军',source_ids='S19'))

data=dict(user=[],team=teams,player=players,team_player=roster,coach=coaches,team_coach=tc,venue=venues,football_match=matches,player_match_stat=stats,honor=honors,team_honor=th,comment=[])
package=dict(metadata=dict(title='2026世界杯精选6队精简数据包',researched_on='2026-10-28',snapshot='2026世界杯赛事期间；教练及号码不代表当前现任信息',roster_scope='每队4名代表球员，并非完整名单',match_scope='仅8场精选比赛',player_stat_scope='每名球员仅1场已核实比赛；不是其本届赛事总计',media_scope='照片和队徽路径为空，须自行补充实际素材；当前支持占位展示',source_ids_note='source_ids在sources中查阅；为溯源字段，不是业务外键'),sources=source_rows,tables=data)
(OUT/'02_结构化数据.json').write_text(json.dumps(package,ensure_ascii=False,indent=2),encoding='utf-8')

# 定义同时用于生成可导入MySQL SQL、数据字典及SQLite结构关系检查。
schemas={
 'user': [('id','INT','主键'),('username','VARCHAR(255)','管理员账号'),('auth_key','VARCHAR(32)','Yii2认证密钥'),('password_hash','VARCHAR(255)','Yii2兼容密码散列'),('password_reset_token','VARCHAR(255)','重置令牌，可空'),('email','VARCHAR(255)','管理员邮箱'),('status','SMALLINT','Yii2状态'),('created_at','INT','Unix时间戳'),('updated_at','INT','Unix时间戳'),('verification_token','VARCHAR(255)','邮箱令牌，可空')],
 'team':[('id','INT','主键'),('name_zh','VARCHAR(80)','中文球队名'),('name_en','VARCHAR(80)','英文球队名'),('fifa_code','VARCHAR(3)','FIFA三字码，唯一'),('country','VARCHAR(80)','国家或地区'),('continent','VARCHAR(30)','洲'),('introduction','TEXT','自编精简介绍'),('logo_path','VARCHAR(500)','队徽路径，可空'),('source_ids','VARCHAR(255)','来源编号'),('source_page','INT','官方名单页码')],
 'player':[('id','INT','主键'),('name_zh','VARCHAR(80)','中文展示译名'),('name_en','VARCHAR(100)','英文常用名'),('nationality','VARCHAR(80)','国籍'),('birth_date','DATE','出生日期'),('photo_path','VARCHAR(500)','照片路径，可空'),('source_ids','VARCHAR(255)','来源编号'),('source_page','INT','官方名单页码')],
 'team_player':[('id','INT','主键'),('team_id','INT','球队外键'),('player_id','INT','球员外键'),('tournament_year','INT','赛事年份2026'),('shirt_number','INT','本届号码1至26'),('position_code','VARCHAR(2)','GK/DF/MF/FW'),('source_ids','VARCHAR(255)','来源编号')],
 'coach':[('id','INT','主键'),('name_zh','VARCHAR(80)','中文展示译名'),('name_en','VARCHAR(100)','英文常用名'),('nationality','VARCHAR(80)','国籍'),('introduction','TEXT','自编简介，明确赛事快照'),('photo_path','VARCHAR(500)','照片路径，可空'),('source_ids','VARCHAR(255)','来源编号'),('source_page','INT','官方名单页码')],
 'team_coach':[('id','INT','主键'),('team_id','INT','球队外键'),('coach_id','INT','教练外键'),('tournament_year','INT','赛事年份'),('role','VARCHAR(30)','head_coach'),('source_ids','VARCHAR(255)','来源编号')],
 'venue':[('id','INT','主键'),('name_zh','VARCHAR(80)','中文场地名'),('name_en','VARCHAR(100)','FIFA赛事场地名'),('city','VARCHAR(80)','实际城市，可能不同于营销城市名'),('country','VARCHAR(80)','国家'),('timezone','VARCHAR(50)','IANA时区'),('source_ids','VARCHAR(255)','来源编号')],
 'football_match':[('id','INT','主键'),('fifa_match_number','INT','官方比赛编号，唯一'),('tournament_year','INT','赛事年份'),('stage','VARCHAR(30)','比赛阶段枚举'),('venue_id','INT','场地外键'),('home_team_id','INT','本库球队外键；库外可空'),('away_team_id','INT','本库球队外键；库外可空'),('home_team_name','VARCHAR(80)','主队展示名称快照'),('away_team_name','VARCHAR(80)','客队展示名称快照，含库外对手'),('local_match_date','DATE','当地日期，不直接当北京时间'),('kickoff_utc','DATETIME','UTC开球时间；由来源时间及时差换算'),('match_date_beijing','DATE','北京时间日期，网站显示和筛选使用'),('status','VARCHAR(20)','not_started/live/finished/postponed/cancelled'),('home_score','INT','主队比分；未知为NULL'),('away_score','INT','客队比分；未知为NULL'),('after_extra_time','SMALLINT','是否含加时：0/1；点球数不混入比分'),('source_ids','VARCHAR(255)','来源编号')],
 'player_match_stat':[('id','INT','主键'),('match_id','INT','比赛外键'),('team_player_id','INT','本届阵容外键，由此取得球队与球员'),('appeared','SMALLINT','是否实际出场，0/1'),('goals','INT','本场进球数'),('assists','INT','本场助攻；未核实为NULL，不是0'),('source_ids','VARCHAR(255)','来源编号')],
 'honor':[('id','INT','主键'),('name','VARCHAR(80)','荣誉名称'),('competition','VARCHAR(80)','所属赛事'),('source_ids','VARCHAR(255)','来源编号')],
 'team_honor':[('id','INT','主键'),('team_id','INT','球队外键'),('honor_id','INT','荣誉外键'),('year','INT','获得年份'),('description','VARCHAR(255)','荣誉说明'),('source_ids','VARCHAR(255)','来源编号')],
 'comment':[('id','INT','主键'),('team_id','INT','球队留言目标，可空'),('player_id','INT','球员留言目标，可空'),('match_id','INT','比赛留言目标，可空'),('nickname','VARCHAR(30)','昵称1至30字'),('content','TEXT','正文1至1000字'),('status','VARCHAR(20)','pending/approved/rejected/deleted'),('created_at','DATETIME','创建时间'),('reviewed_by','INT','审核管理员外键，可空'),('reviewed_at','DATETIME','审核时间，可空')],
}
fks={
 'team_player':[('team_id','team'),('player_id','player')],
 'team_coach':[('team_id','team'),('coach_id','coach')],
 'football_match':[('venue_id','venue'),('home_team_id','team'),('away_team_id','team')],
 'player_match_stat':[('match_id','football_match'),('team_player_id','team_player')],
 'team_honor':[('team_id','team'),('honor_id','honor')],
 'comment':[('team_id','team'),('player_id','player'),('match_id','football_match'),('reviewed_by','user')],
}
uniques={'user':[['username'],['email'],['password_reset_token']],'team':[['fifa_code']],'team_player':[['team_id','player_id','tournament_year'],['team_id','tournament_year','shirt_number']],'team_coach':[['team_id','coach_id','tournament_year']],'football_match':[['fifa_match_number']],'player_match_stat':[['match_id','team_player_id']],'team_honor':[['team_id','honor_id','year']]}
nullable={'password_reset_token','verification_token','logo_path','photo_path','assists','home_team_id','away_team_id','home_score','away_score','reviewed_by','reviewed_at'}
def ddl(table,mysql=True):
 fields=[]
 for col,typ,meaning in schemas[table]:
  if col=='id': fields.append('`id` INT NOT NULL '+('AUTO_INCREMENT ' if mysql else '')+'PRIMARY KEY')
  else:
   isnull=col in nullable or (table=='comment' and col in ('team_id','player_id','match_id'))
   fields.append(f'`{col}` {typ}'+(' NULL' if isnull else ' NOT NULL'))
 for cols in uniques.get(table,[]): fields.append('UNIQUE ('+', '.join('`'+c+'`' for c in cols)+')')
 for col,target in fks.get(table,[]): fields.append(f'FOREIGN KEY (`{col}`) REFERENCES `{target}` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT')
 tail=' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' if mysql else ''
 return 'CREATE TABLE `'+table+'` (\n  '+',\n  '.join(fields)+'\n)'+tail+';'
def value(v):
 if v is None:return 'NULL'
 if isinstance(v,(int,float)):return str(v)
 return "'"+v.replace("'","''")+"'"
sql=['-- 2026世界杯精选数据：仅向新建空数据库导入。无DROP、无默认密码。', '-- 创建12张表；不创建管理员、不导入虚构留言。', '-- MySQL 5.7/8.x常用语法；未在真实MySQL服务执行，已做SQLite关系镜像检查。', 'SET NAMES utf8mb4;']
sql += [ddl(t) for t in data]
sql+=['START TRANSACTION;']
for t,rows in data.items():
 for row in rows:
  sql.append('INSERT INTO `'+t+'` ('+', '.join('`'+k+'`' for k in row)+') VALUES ('+', '.join(value(v) for v in row.values())+');')
sql+=['COMMIT;']
(OUT/'03_建表与真实资料导入.sql').write_text('\n\n'.join(sql)+'\n',encoding='utf-8')

stats_sql='''-- 团队胜平负及进失球：仅已导入且已结束、比分完整的2026比赛；含库外对手。
SELECT t.id, t.name_zh,
 COUNT(m.id) AS played,
 COALESCE(SUM(CASE WHEN (m.home_team_id=t.id AND m.home_score>m.away_score) OR (m.away_team_id=t.id AND m.away_score>m.home_score) THEN 1 ELSE 0 END),0) AS wins,
 COALESCE(SUM(CASE WHEN m.home_score=m.away_score THEN 1 ELSE 0 END),0) AS draws,
 COALESCE(SUM(CASE WHEN (m.home_team_id=t.id AND m.home_score<m.away_score) OR (m.away_team_id=t.id AND m.away_score<m.home_score) THEN 1 ELSE 0 END),0) AS losses,
 COALESCE(SUM(CASE WHEN m.home_team_id=t.id THEN m.home_score WHEN m.away_team_id=t.id THEN m.away_score ELSE 0 END),0) AS goals_for,
 COALESCE(SUM(CASE WHEN m.home_team_id=t.id THEN m.away_score WHEN m.away_team_id=t.id THEN m.home_score ELSE 0 END),0) AS goals_against
FROM team t LEFT JOIN football_match m
 ON (m.home_team_id=t.id OR m.away_team_id=t.id)
 AND m.tournament_year=2026 AND m.status='finished'
 AND m.home_score IS NOT NULL AND m.away_score IS NOT NULL
GROUP BY t.id,t.name_zh ORDER BY t.id;

-- 球员资料与已收录统计；没有统计记录时场次/进球保持NULL，页面显示“未收录”。
SELECT p.id,p.name_zh,t.name_zh AS team_name,tp.shirt_number,tp.position_code,
 s.recorded_matches,s.recorded_appearances,s.recorded_goals
FROM player p JOIN team_player tp ON tp.player_id=p.id AND tp.tournament_year=2026
JOIN team t ON t.id=tp.team_id
LEFT JOIN (
 SELECT ps.team_player_id,COUNT(*) AS recorded_matches,SUM(ps.appeared) AS recorded_appearances,SUM(ps.goals) AS recorded_goals
 FROM player_match_stat ps JOIN football_match m ON m.id=ps.match_id
 WHERE m.tournament_year=2026 AND m.status='finished'
 GROUP BY ps.team_player_id
) s ON s.team_player_id=tp.id ORDER BY p.id;
'''
(OUT/'04_球队战绩与球员统计查询.sql').write_text(stats_sql,encoding='utf-8')

# 本地关系与业务校验；不把SQLite验证表述为MySQL实机验证。
db=sqlite3.connect(':memory:');db.execute('PRAGMA foreign_keys=ON')
for t in data:db.executescript(ddl(t,False))
for t,rows in data.items():
 for row in rows:
  db.execute('INSERT INTO `'+t+'` ('+','.join('`'+k+'`' for k in row)+') VALUES ('+','.join('?' for _ in row)+')',list(row.values()))
assert not db.execute('PRAGMA foreign_key_check').fetchall()
assert len(data)==12 and len(teams)==6 and len(players)==24 and len(stats)==24
sources_known={s['source_id'] for s in source_rows}
for table,rows in data.items():
 for r in rows:
  assert set(r.get('source_ids','').split(';')) <= sources_known if 'source_ids' in r else True
  if table=='player': datetime.fromisoformat(r['birth_date'])
for r in stats:
 m=next(m for m in matches if m['id']==r['match_id']);tp=roster[r['team_player_id']-1]
 assert tp['team_id'] in (m['home_team_id'],m['away_team_id'])
 assert r['goals'] <= (m['home_score'] if tp['team_id']==m['home_team_id'] else m['away_score'])
for m in matches:
 assert m['home_team_id'] or m['away_team_id']
 assert m['home_team_id']!=m['away_team_id']
 assert m['home_score']>=0 and m['away_score']>=0
summaries=db.execute(stats_sql.split('-- 球员资料')[0]).fetchall()
for row in summaries:assert row[2]==row[3]+row[4]+row[5]
db.executescript(stats_sql)

overview='''# 队名建议与六支球队推荐

整理日期：2026年10月28日。开发小组队名与网站收录的国家队是两个概念；下列名称只是备选，没有替你最终确定。

## 一、开发小组队名建议

| 队名 | 含义及适用性 |
| --- | --- |
| 球数有据（首推） | 足球＋数据＋有据可查，简洁，适合数据库课程，也可作网站名 |
| 绿茵数研队 | 足球主题明确，又体现数据整理与开发 |
| 足迹数据库 | 适合以球队、球员和比赛记录为核心的网站 |
| 津门绿茵队 | 结合天津地域与足球主题，适合作为开发团队名 |
| 三人传控队 | 呼应三人团队及协作开发，偏轻松 |
| 终场有数队 | 呼应比赛结果与统计图表，容易记忆 |

推荐组合：开发团队“球数有据”，网站“球数有据——2026世界杯精选球队数据库”。可用于文件名“球数有据_需求文档(学号1_学号2_学号3).pdf”。

## 二、推荐的六支国家队

选择依据是资料可获得性、代表球员辨识度，以及本包精选比赛能形成球队间关联，属于项目选材建议，不是实时实力排名。球员来自FIFA赛事名单[S01]。

| ID | 球队 | 精选4名球员 | 建站用途 |
| --- | --- | --- | --- |
| 1 | 阿根廷 ARG | 马丁内斯、罗梅罗、恩佐、梅西 | 球星、冠军荣誉；与英格兰和西班牙关联 |
| 2 | 法国 FRA | 迈尼昂、孔德、楚阿梅尼、姆巴佩 | 球员与比赛关联；与西班牙和英格兰关联 |
| 3 | 西班牙 ESP | 西蒙、库巴西、佩德里、亚马尔 | 可串联葡萄牙、法国和阿根廷三个对手 |
| 4 | 巴西 BRA | 阿利松、马尔基尼奥斯、吉马良斯、维尼修斯 | 样本包含胜平负，适合测试饼图；历史荣誉丰富 |
| 5 | 葡萄牙 POR | 科斯塔、迪亚斯、布鲁诺、C罗 | 与西班牙直接对比；欧洲杯荣誉展示 |
| 6 | 英格兰 ENG | 皮克福德、斯通斯、贝林厄姆、凯恩 | 与阿根廷和法国关联；前后台操作样本 |

每队刻意只选门将、后卫、中场、前锋各1人，合计24人，覆盖位置筛选。它们是“精选球员”，不是26人完整阵容，网站应如此标注。

## 三、数据包包含什么

- 6支球队、24名球员及24条阵容关系。
- 6名赛事期间教练及6条执教关系。
- 5个比赛场地、8场精选真实比赛。
- 24条球员单场记录，每名精选球员各1场；已核对出场与进球，助攻未核实留NULL。
- 2类荣誉及14条球队夺冠记录。
- 12张表的建表SQL、真实资料INSERT、统计查询、数据字典和ER图源文件。
- 管理员及真实访客留言没有公开赛事来源，因此业务表留空；另附明确标为虚构的留言测试样例。

## 四、使用顺序

1. 阅读本文件及“05_数据字典与ER关系.md”，三人共同确认字段与库外对手设计。
2. 由数据库负责人新建空MySQL数据库，用UTF-8打开“03_建表与真实资料导入.sql”执行。脚本不包含DROP，不会主动清库；重复执行会报表已存在，避免无提示覆盖。
3. 如果已有Yii2的user表，不直接运行整份SQL：保留原user表，在设计评审后跳过SQL中对应CREATE TABLE；其余字段、前缀也须与实际代码匹配。
4. 执行“04_球队战绩与球员统计查询.sql”核对列表与图表数据。球队统计覆盖本包所录8场比赛；球员统计只覆盖本包已核实的单场记录。
5. 使用Yii2初始化流程创建管理员，调用框架生成密码散列和auth_key，不把明文密码直接写入user表。
6. 根据“06_来源与待补素材.md”补齐实际照片、队徽。资料包没有伪造图片URL，当前照片及队徽路径均为NULL，页面先显示占位图。
7. 最终实现后再从实际数据库导出课程要求的 `/data/install.sql`；本文件夹的准备SQL不是声称已生成的正式部署文件。

## 五、关键口径

- 这是2026世界杯赛事快照，不是2026年10月现任教练、当前俱乐部或实时国家队排名。为减少变动，未纳入俱乐部、身价、排名和累计国家队出场数据。
- 球队比较和饼图只能标“本站已收录比赛”。个人数据只能标“本站已核实球员场次”，不能声称是整届世界杯总进球。
- 比分含加时，不含点球大战罚球数。决赛为加时赛结果，在本规则下按终场比分计胜负。
- 日期已按开球UTC换算为北京时间日期；例如当地6月13日的巴西对摩洛哥，在中国显示6月14日。
- 没有球员统计记录不等于0出场；NULL助攻表示未核实，不等于0助攻。选择的24条记录只涵盖24名球员，不要求其进球合计覆盖比赛双方所有进球。
- 库外对手摩洛哥、日本、挪威仅保存名称，不添加为第7—9支重点球队，不补录其球员。
- 中文译名、精简简介及推荐理由为整理者编写；生日、名单、比分、荣誉等事实保留来源编号。

## 六、先交给谁

- 组长A：选定队名；确认6队及精简范围；把字段与需求差异列入共同评审。
- 组员B：以JSON、SQL和数据字典为设计底稿，确认外键、表名和来源字段。
- 组员C：依据8场比赛及统计查询开发比赛、对比、饼图；不要把8场当作完整赛程。

资料基于本次公开网络检索，优先FIFA、足协、联赛和俱乐部官方网站，逐条出处见来源清单。少量球员单场数据使用可读取的FBref搜索索引交叉核验，并已明确标记读取范围。SQL目前只完成本地结构关系镜像校验，尚未连接项目MySQL做实机导入。
'''
(OUT/'01_队名建议与球队推荐.md').write_text(overview,encoding='utf-8')

dictionary=['# 数据字典与ER关系\n','本稿为设计起点，不是最终已实施数据库。建议12张表，匹配需求与三人分工；无需增加新闻等范围外模块。\n', '## 表记录数\n','| 表 | 行数 | 说明 |\n| --- | ---: | --- |']
for t,rows in data.items():dictionary.append(f'| `{t}` | {len(rows)} | '+('项目初始化创建；不属于公开赛事资料' if t in ('user','comment') else '精选样本，详见来源')+' |')
dictionary+=['\n## 字段定义\n','主键均为自增整数；ID仅是本包内部标识，只有fifa_match_number是FIFA比赛编号。source_ids是分号分隔的来源编号，映射JSON中sources；不计为额外业务表。']
for t,fields in schemas.items():
 dictionary+=['\n### '+t+'\n','| 字段 | SQL类型 | 说明 |\n| --- | --- | --- |']
 for col,typ,meaning in fields:dictionary.append(f'| `{col}` | `{typ}` | {meaning} |')
 dictionary.append('外键：'+('；'.join(f'`{c}` → `{target}.id`（RESTRICT）' for c,target in fks.get(t,[])) or '无'))
 dictionary.append('唯一约束：'+('；'.join(' + '.join(cols) for cols in uniques.get(t,[])) or '除主键外无'))
dictionary+=['''
## ER图

ER图源文件也单独提供为“07_ER图.mmd”。适合粘贴到支持Mermaid的Markdown或ER图工具，再补字段和布局。

```mermaid
erDiagram
    team ||--o{ team_player : has
    player ||--o{ team_player : joins
    team ||--o{ team_coach : appoints
    coach ||--o{ team_coach : coaches
    venue ||--o{ football_match : hosts
    team o|--o{ football_match : home_team
    team o|--o{ football_match : away_team
    football_match ||--o{ player_match_stat : records
    team_player ||--o{ player_match_stat : performs
    team ||--o{ team_honor : earns
    honor ||--o{ team_honor : defines
    team o|--o{ comment : target_team
    player o|--o{ comment : target_player
    football_match o|--o{ comment : target_match
    user o|--o{ comment : reviews
```

图中的可选球队关系用于库外对手；留言三种对象是“恰好选一”，不是同时三选。图不能表达的条件由应用规则约束。

## 必须同步到Yii2校验的规则

1. 比赛至少有一侧关联本库球队；两侧皆为库内时不得相同。库外一侧ID为空但名称必填；库内名称由球队资料写入，避免与ID矛盾。
2. 比赛、阵容和球员统计属于同一赛事年份；统计球员所属球队必须等于该场主队或客队ID。
3. 比分非负，finished状态计入统计前必须双方比分完整；未知为NULL。状态及阶段使用枚举白名单，不接收随意字符串。
4. 单场球员进球为非负数且不超过其球队该场比分；未出场球员不得记正数进球。同一场与阵容关系唯一。
5. 阵容位置仅GK、DF、MF、FW；每队每届球衣号码唯一。出生日期必须为有效日期。
6. 每条留言的team_id、player_id、match_id必须恰有一个非空，且对象真实存在。昵称1—30字，正文1—1000字，审核状态白名单；reviewed_by必须引用管理员。
7. 所有后台接口检查管理员身份。密码用Yii2散列。留言安全转义，上传校验文件类型与大小。
8. 使用FK RESTRICT阻止直接删除有历史关联的数据；在界面给出依赖提示。上传图片不存在时显示占位图。
9. MySQL 5.7不会可靠执行CHECK约束，因此本准备SQL不靠CHECK保证上述跨表规则，必须写入模型校验或服务事务。

## 与分工中的简化方案差异

- player_match_stat使用team_player_id关联本届阵容，从关系取得球员及球队，避免重复保存player_id和team_id发生矛盾。
- comment采用三个可空目标外键，不用无法直接建立外键的多态target_id；恰好选一由程序校验。
- 比赛增加UTC、当地日期及北京时间日期，解决跨日；用户页面仍只需显示北京时间日期。
- 库外球队只存名称，满足当前需求，不强制补录为重点球队。
- 这些是便于导入与画ER图的建议，三人确认后再同步至正式设计文档及代码。
''']
(OUT/'05_数据字典与ER关系.md').write_text('\n'.join(dictionary),encoding='utf-8')
mermaid=re.search(r'```mermaid\n(.*?)\n```','\n'.join(dictionary),re.S).group(1)
(OUT/'07_ER图.mmd').write_text(mermaid+'\n',encoding='utf-8')

source_doc=['# 来源、缺失素材与使用边界\n','检索日：2026年10月28日。公开网页可能更新，需按本包赛事年份解读。链接为核对入口，不等于图片授权或实时接口。\n','## 来源清单\n']
for s in source_rows:
 source_doc += [f'### {s["source_id"]} [{s["title"]}]({s["url"]})',s['scope'],f'读取范围：{s["access_note"]}。\n']
source_doc+=['''
## 尚需补齐的非结构性素材

| 素材 | 数量 | 当前状态 | 处理方法 |
| --- | ---: | --- | --- |
| 球队队徽 | 6 | logo_path=NULL | 从所选球队足协媒体页选择合适素材，记录来源，保存为项目内文件后更新路径 |
| 球员照片 | 24 | photo_path=NULL | 通过官方球员或俱乐部页面选择对应人物照片；不要把集体照当个人头像 |
| 教练照片 | 6 | photo_path=NULL | 从足协或教练官方介绍页选取；可先使用中性占位图 |
| 管理员账号 | 至少1 | user表为空 | 在Yii2初始化流程创建，不使用虚构或明文密码 |
| 留言 | 按测试需要 | comment表为空 | 另附虚构样例，仅用于本地测试，真实运行后由访客产生 |
| 作业下载文件 | 团队及个人成果 | 不属于赛事数据 | 使用实际交付文件，按分工方案建立受控下载清单 |

上表意味着当前包覆盖全部业务实体与必需字段，但尚未提供全部图片二进制素材、项目账号或完整赛事记录。后续补图不会改变ER关系。

## 日期核验与冲突处理

比赛当地时间依据逐场官方信息，结合场地夏令时转换为UTC与北京时间日期。纽约、亚特兰大、迈阿密采用UTC-4；达拉斯与休斯敦采用UTC-5，仅适用于本包2026年6—7月记录。

FIFA部分汇总搜索索引对M76及部分淘汰赛日期出现与逐场足协来源不同的展示。本包M76采用日本足协和巴西足协共同给出的当地6月29日12:00，不照抄汇总索引中的6月28日。所有日期差异均保留对应来源，核验报告检查北京时间跨日换算。

M93的FBref页面直开被403限制，但搜索结果实际返回了比分、时间及本包四位葡萄牙球员首发，故只使用这些已显示字段，不声称读取了完整技术统计。若提交前需要完整逐场证明，可再次从足协比赛中心核对；本包没有估计分钟数或助攻。

## 统计与完整性

- 球队统计来自8场比赛，球员统计来自每位球员选定的1场比赛，覆盖范围不同，界面需分别标注。
- 除精选24人外，其他进球者不在本包球员表中，因此不要用精选球员进球和校验整个比赛的球队总进球。
- 不包含实时排名、身价、转会或当前俱乐部，避免把比赛快照和现状混合。
- 队名建议、中文译名、介绍及说明为自行整理，未复制长篇官方文案。
''']
(OUT/'06_来源与待补素材.md').write_text('\n'.join(source_doc),encoding='utf-8')

demo=dict(note='以下是虚构的本地测试留言，非网络抓取的真实用户内容；不会自动写入真实资料SQL。管理员须先通过Yii2创建。',comments=[dict(team_id=1,player_id=None,match_id=None,nickname='测试访客A',content='本条用于验证球队留言审核流程。',status='pending'),dict(team_id=None,player_id=12,match_id=None,nickname='测试访客B',content='本条用于验证球员留言展示。',status='approved'),dict(team_id=None,player_id=None,match_id=104,nickname='测试访客C',content='本条用于验证比赛留言审核不通过的状态。',status='rejected')])
(OUT/'08_虚构留言测试样例.json').write_text(json.dumps(demo,ensure_ascii=False,indent=2),encoding='utf-8')
report=['# 数据校验报告\n','检验对象：本资料包生成的数据。真实资料通过列出的公开来源人工整理；自动检查只验证结构、引用和计算，不等于再次独立核实网络事实。\n', '## 已通过\n','- 12张表定义、6支球队、24名球员、24条阵容关系、6名教练、6条执教关系、5个场地、8场比赛、24条球员统计、2类荣誉、14条荣誉记录。','- 所有JSON来源编号存在；外键无悬空引用。','- 球衣号码与阵容唯一约束通过。','- 球员单场统计所属球队属于该场参赛方；进球不超过球队比分。','- 比分非负；比赛至少含一支本库球队；北京时间跨日转换完成。','- 统计查询已在SQLite结构镜像执行，胜＋平＋负＝收录场次。','- 结构JSON解析及SQL INSERT在镜像库中逐行导入通过。\n','## 球队样本统计核对\n','| 球队 | 场次 | 胜 | 平 | 负 | 进球 | 失球 |\n| --- | ---: | ---: | ---: | ---: | ---: | ---: |']
for row in summaries:report.append('| '+' | '.join(str(v) for v in row[1:])+' |')
report+=['\n## 精选比赛日期核对\n','| 编号 | 比赛 | 当地日期 | 北京时间日期 | 比分 |\n| --- | --- | --- | --- | --- |']
for m in matches:report.append(f'| {m["id"]} | {m["home_team_name"]}—{m["away_team_name"]} | {m["local_match_date"]} | {m["match_date_beijing"]} | {m["home_score"]}:{m["away_score"]} |')
report+=['\n## 验证限制\n','未启动或连接实际MySQL服务，未在现有Yii2项目执行迁移与页面测试。MySQL建表语法已按常用兼容写法生成；导入前仍须在独立空MySQL库验证。图片实际文件未提供；管理员及真实留言未生成。此SQL是资料准备稿，不能直接声称已完成课程正式部署还原。']
(OUT/'09_数据校验报告.md').write_text('\n'.join(report),encoding='utf-8')
manifest=[]
for p in sorted(OUT.iterdir()):
 if p.is_file() and p.name!='文件校验清单.json':manifest.append(dict(file=p.name,bytes=p.stat().st_size,sha256=hashlib.sha256(p.read_bytes()).hexdigest()))
(OUT/'文件校验清单.json').write_text(json.dumps(manifest,ensure_ascii=False,indent=2),encoding='utf-8')
print(json.dumps(dict(output=str(OUT.resolve()),counts={t:len(rows) for t,rows in data.items()},files=len(manifest)+1,validation='passed; SQLite mirror only'),ensure_ascii=False))
