<?php
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class action extends app
{
	public function display()
	{
		$action = $this->ev->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		if($this->data['currentbasic']['basicexam']['opentime']['start'] && $this->data['currentbasic']['basicexam']['opentime']['end'])
		{
			if($this->data['currentbasic']['basicexam']['opentime']['start'] > TIME ||  $this->data['currentbasic']['basicexam']['opentime']['end'] < TIME)
			$action = 'index';
		}
		$this->$action();
		exit;
	}

	private function reload()
	{
		$args = array('examsessionkey' => 0);
		$this->exam->modifyExamSession($args);
		header("location:index.php?exam-app-exam");
	}

	private function ajax()
	{
		switch($this->ev->url(4))
		{
			//获取剩余考试时间
			case 'getexamlefttime':
			//$sessionvars = $this->exam->getExamSessionBySessionid();
			$lefttime = 0;
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($this->data['currentbasic']['basicexam']['opentime']['start'] && $this->data['currentbasic']['basicexam']['opentime']['end'])
			$t = $this->data['currentbasic']['basicexam']['opentime']['end']-300;
			else
			$t = TIME;
			$lefttime = $t - $sessionvars['examsessionstarttime'];
			if($lefttime < 0 )$lefttime = 0;
			exit("{$lefttime}");
			break;

			case 'saveUserAnswer':
			$question = $this->ev->post('question');
			foreach($question as $key => $t)
			{
				if($t == '')unset($question[$key]);
			}
			$this->exam->modifyExamSession(array('examsessionuseranswer'=>$question));
			echo is_array($question)?count($question):0;
			exit;
			break;

			default:
		}
	}

	private function view()
	{
		$sessionvars = $this->exam->getExamSessionBySessionid();
		if($sessionvars['examsessiontype'] != 2)
		{
			if($sessionvars['examsessiontype'])
			header("location:index.php?exam-app-exampaper-view");
			else
			header("location:index.php?exam-app-exercise-view");
			exit;
		}
		$this->tpl->assign('questype',$this->basic->getQuestypeList());
		$this->tpl->assign('sessionvars',$sessionvars);
		$this->tpl->display('exam_view');
	}

	private function makescore()
	{
		$questype = $this->basic->getQuestypeList();
		$sessionvars = $this->exam->getExamSessionBySessionid();
		if($this->ev->get('makescore'))
		{
			$score = $this->ev->get('score');
			$sumscore = 0;
			if(is_array($score))
			{
				foreach($score as $key => $p)
				{
					$sessionvars['examsessionscorelist'][$key] = $p;
				}
			}
			foreach($sessionvars['examsessionscorelist'] as $p)
			{
				$sumscore = $sumscore + floatval($p);
			}
			$sessionvars['examsessionscore'] = $sumscore;
			$args['examsessionscorelist'] = $sessionvars['examsessionscorelist'];
			$allnumber = floatval(count($sessionvars['examsessionscorelist']));
			$args['examsessionscore'] = $sessionvars['examsessionscore'];
			$args['examsessionstatus'] = 2;
			$this->exam->modifyExamSession($args);
			$id = $this->favor->addExamHistory();
			if($this->ev->get('direct'))
			{
				if($id)
				header("location:index.php?exam-app-exam-makescore&ehid={$id}");
				else
				header("location:index.php?exam-app-exam-paper");
				exit;
			}
			else
			{
				if($id)
				$message = array(
					'statusCode' => 200,
					"message" => "操作成功",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-makescore&ehid={$id}"
				);
				else
				$message = array(
					'statusCode' => 300,
					"message" => "操作失败，请重新提交"
				);
				$this->G->R($message);
			}
		}
		else
		{
			$ehid = $this->ev->get('ehid');
			$eh = $this->favor->getExamHistoryById($ehid);
			$sessionvars = array(
				'examsession' => $eh['ehexam'],
				'examsessiontype'=> $eh['ehtype'] == 2?1:$eh['ehtype'],
				'examsessionsetting'=> $eh['ehsetting'],
				'examsessionbasic'=> $eh['ehbasicid'],
				'examsessionquestion'=> $eh['ehquestion'],
				'examsessionuseranswer'=>$eh['ehanswer'],
				'examsessiontime'=> $eh['ehtime'],
				'examsessionscorelist'=> $eh['ehscorelist'],
				'examsessionscore'=>$eh['ehscore'],
				'examsessionstarttime'=>$eh['ehstarttime']
			);
			$number = array();
			$right = array();
			$score = array();
			$allnumber = 0;
			$allright = 0;
			foreach($questype as $key => $q)
			{
				$number[$key] = 0;
				$right[$key] = 0;
				$score[$key] = 0;
				if($sessionvars['examsessionquestion']['questions'][$key])
				{
					foreach($sessionvars['examsessionquestion']['questions'][$key] as $p)
					{
						$number[$key]++;
						$allnumber++;
						if($sessionvars['examsessionscorelist'][$p['questionid']] == $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'])
						{
							$right[$key]++;
							$allright++;
						}
						$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
					}
				}
				if($sessionvars['examsessionquestion']['questionrows'][$key])
				{
					foreach($sessionvars['examsessionquestion']['questionrows'][$key] as $v)
					{
						foreach($v['data'] as $p)
						{
							$number[$key]++;
							$allnumber++;
							if($sessionvars['examsessionscorelist'][$p['questionid']] == $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'])
							{
								$right[$key]++;
								$allright++;
							}
							$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
						}
					}
				}
			}
			$this->tpl->assign('ehid',$ehid);
			$this->tpl->assign('allright',$allright);
			$this->tpl->assign('allnumber',$allnumber);
			$this->tpl->assign('right',$right);
			$this->tpl->assign('score',$score);
			$this->tpl->assign('number',$number);
			$this->tpl->assign('questype',$questype);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->display('exam_score');
		}
	}

	private function score()
	{
		$questype = $this->basic->getQuestypeList();
		$sessionvars = $this->exam->getExamSessionBySessionid();
		$needhand = 0;
		if($this->data['currentbasic']['basicexam']['examnumber'])
		{
			$overflow = false;
			$ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
			if(!$ids)$ids = '0';
			$number = array();
			if($ids)
			{
				$ids = explode(',',$ids);
				foreach($ids as $t)
				{
					$num = $this->favor->getExamUseNumber($this->_user['sessionuserid'],$t,$this->data['currentbasic']['basicid']);
					$number['child'][$t] = $num;
					$number['all'] = intval($number['all'])+$num;
				}
			}
			if($this->data['currentbasic']['basicexam']['selectrule'])
			{
				if($number['all'] >= $this->data['currentbasic']['basicexam']['examnumber'])
				{
					$overflow = true;
				}
			}
			else
			{
				if($number['child'][$sessionvars['examsessionkey']] >= $this->data['currentbasic']['basicexam']['examnumber'])
				{
					$overflow = true;
				}
			}
			if($overflow)
			{
				$message = array(
					'statusCode' => 300,
					"message" => "您的考试次数已经用完了！"
				);
				$this->G->R($message);
			}
		}
		if($this->ev->get('insertscore'))
		{
			$question = $this->ev->get('question');
			$time = $this->ev->get('time');
			foreach($question as $key => $a)
			$sessionvars['examsessionuseranswer'][$key] = $a;
			foreach($sessionvars['examsessionquestion']['questions'] as $key => $tmp)
			{
				if(!$questype[$key]['questsort'])
				{
					foreach($tmp as $p)
					{
						if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
						{
							$nanswer = '';
							$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
							asort($answer);
							$nanswer = implode("",$answer);
							if($nanswer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
							else
							{
								if($questype[$key]['questchoice'] == 3)
								{
									$alen = strlen($p['questionanswer']);
									$rlen = 0;
									foreach($answer as $t)
									{
										if(strpos($p['questionanswer'],$t) === false)
										{
											$rlen = 0;
											break;
										}
										else
										{
											$rlen ++;
										}
									}
									$score = floatval($sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'] * $rlen/$alen);
								}
								else $score = 0;
							}
						}
						else
						{
							$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
							if($answer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
							else $score = 0;
						}
						$scorelist[$p['questionid']] = $score;
					}
				}
				else
				{
					if(is_array($tmp) && count($tmp))
					$needhand = 1;
				}
			}
			foreach($sessionvars['examsessionquestion']['questionrows'] as $key => $tmp)
			{
				if(!$questype[$key]['questsort'])
				{
					foreach($tmp as $tmp2)
					{
						foreach($tmp2['data'] as $p)
						{
							if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
							{
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								asort($answer);
								$nanswer = implode("",$answer);
								if($nanswer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
								else
								{
									if($questype[$key]['questchoice'] == 3)
									{
										$alen = strlen($p['questionanswer']);
										$rlen = 0;
										foreach($answer as $t)
										{
											if(strpos($p['questionanswer'],$t) === false)
											{
												$rlen = 0;
												break;
											}
											else
											{
												$rlen ++;
											}
										}
										$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'] * $rlen/$alen;
									}
									else $score = 0;
								}
							}
							else
							{
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								if($answer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
								else $score = 0;
							}
							$scorelist[$p['questionid']] = $score;
						}
					}
				}
				else
				{
					if(!$needhand)
					{
						if(is_array($tmp) && count($tmp))
						$needhand = 1;
					}
				}
			}
			$args['examsessionuseranswer'] = $question;
			$args['examsessiontimelist'] = $time;
			$args['examsessionscorelist'] = $scorelist;
			if(!$needhand)
			{
				$args['examsessionstatus'] = 2;
				$args['examsessionscore'] = array_sum($scorelist);
				$this->exam->modifyExamSession($args);
				$message = array(
					'statusCode' => 200,
					"message" => "操作成功",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-makescore&makescore=1&direct=1"
				);
			}
			else
			{
				if($sessionvars['examsessionsetting']['examdecide'])
				{
					$args['examsessionstatus'] = 2;
					$this->exam->modifyExamSession($args);
					$id = $this->favor->addExamHistory(0,0);
					if($id)
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功，本试卷需要教师评分，请等待评分结果",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-history&ehtype=2"
					);
					else
					$message = array(
						'statusCode' => 300,
						"message" => "操作失败，请重新提交"
					);
				}
				else
				{
					$args['examsessionstatus'] = 1;
					$this->exam->modifyExamSession($args);
					//$this->favor->addExamHistory(1);
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exam-score"
					);
				}
			}
			$this->G->R($message);
		}
		else
		{
			if($sessionvars['examsessionstatus'] == 2)
			{
				header("location:index.php?exam-app-exam-makescore");
				exit;
			}
			else
			{
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->assign('questype',$questype);
				$this->tpl->display('exam_mkscore');
			}
		}
	}
	
	
	/**
     * AI 安全大模型分析接口 (修复版：兼容低版本 PHP)
     */
    public function getanalysis()
    {
        // 1. 安全接收参数
        $ehid = $this->ev->get('ehid');
        if(!$ehid) exit(json_encode(array('status' => 'error', 'msg' => '参数错误')));

        // 2. 获取考试记录
        $eh = $this->favor->getExamHistoryById($ehid);
        if(!$eh) exit(json_encode(array('status' => 'error', 'msg' => '未找到考试记录')));

        // 3. 数据清洗与错题提取
        $questions = unserialize($eh['ehquestion']);
        $scoreList = unserialize($eh['ehscore']);
        
        $wrongData = array();
        $i = 0;

        if(is_array($questions['questions'])){
            foreach($questions['questions'] as $typeid => $rows){
                foreach($rows as $q){
                    // 判定错题
                    $userScore = isset($scoreList[$q['questionid']]) ? $scoreList[$q['questionid']] : 0;
                    
                    if($userScore == 0){
                        // 清洗HTML标签
                        $qContent = strip_tags(html_entity_decode($q['question']));
                        // 截断过长题目
                        $qContent = mb_substr($qContent, 0, 100, 'utf-8'); 
                        $wrongData[] = ($i+1) . "、" . $qContent;
                        $i++;
                        if($i >= 8) break 2; 
                    }
                }
            }
        }

        // 4. 如果全对的处理
        if(empty($wrongData)){
            $res = "恭喜！本次安全考核您全部通过，说明您对企业安全生产规范掌握非常牢固。安全无小事，请在日常工作中继续保持警惕！";
            exit(json_encode(array('status' => 'success', 'data' => $res)));
        }

        // 5. 组装 Prompt
        $wrongText = implode("\n", $wrongData);
        $prompt = "你现在是企业安全生产培训的高级专家导师。考生刚刚完成了一次安全生产规范考试，以下是他的错题摘要：\n{$wrongText}\n\n请完成以下任务：\n1. 简要分析该考生在安全意识或操作规范上的具体薄弱点。\n2. 给出3条简短、严肃且具体的整改或学习建议。\n3. 语气要专业、严谨，直接给建议，字数控制在300字以内。";

        // 6. 调用阿里云百炼 API
        // ================= 配置区 START =================
        // 请在此处填入阿里云 API KEY
        $ALIYUN_API_KEY = "sk-2f996bffe9874aa88c93cefb0e572019"; 
        // ================= 配置区 END ===================

        $aiResult = $this->callBailianAi($ALIYUN_API_KEY, $prompt);
        
        // 格式化输出
        $finalHtml = str_replace("\n", "<br/>", $aiResult);
        exit(json_encode(array('status' => 'success', 'data' => $finalHtml)));
    }

    /**
     * 阿里云百炼 API 调用封装 (修复版：兼容 curl 和老版本语法)
     */
    private function callBailianAi($apiKey, $content)
    {
        $url = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
        
        // 请求头
        $headers = array(
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        );

        // 请求体
        $data = array(
            "model" => "qwen-plus", 
            "messages" => array(
                array(
                    "role" => "system",
                    "content" => "你是企业安全生产考核系统的智能助手。"
                ),
                array(
                    "role" => "user",
                    "content" => $content
                )
            ),
            "temperature" => 0.7,
            "stream" => false
        );

        // 发送 CURL 请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $result = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return "AI 服务连接失败: " . $error_msg;
        }
        curl_close($ch);

        // 解析结果
        $response = json_decode($result, true);

        // 错误处理 (这里修复了 ?? 语法错误)
        if(isset($response['error'])) {
            $errMsg = isset($response['error']['message']) ? $response['error']['message'] : '未知错误';
            return "AI 调用报错: " . $errMsg;
        }

        // 提取回复内容
        if(isset($response['choices'][0]['message']['content'])){
            return $response['choices'][0]['message']['content'];
        } else {
            return "AI 响应解析失败，请检查 API Key 或余额。";
        }
    }
	
	

	private function paper()
	{
		$sessionvars = $this->exam->getExamSessionBySessionid();
		$lefttime = 0;
		$questype = $this->basic->getQuestypeList();
		if($sessionvars['examsessionstatus'] == 2)
		{
			header("location:index.php?exam-app-exam-makescore");
			exit;
		}
		elseif($sessionvars['examsessionstatus'] == 1)
		{
			header("location:index.php?exam-app-exam-score");
			exit;
		}
		else
		{
			//$exams = $this->exam->getExamSettingList(1,3,array(array("AND","examsubject = :examsubject",'examsubject',$this->data['currentsubject']['subjectid']),array("AND","examtype = 1")));
			$this->tpl->assign('questype',$questype);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->assign('lefttime',$lefttime);
			$this->tpl->assign('donumber',is_array($sessionvars['examsessionuseranswer'])?count($sessionvars['examsessionuseranswer']):0);
			if($this->data['currentbasic']['basicexam']['selftemplate'])
			$this->tpl->display($this->data['currentbasic']['basicexam']['selftemplate']);
			else
			$this->tpl->display('exam_paper');
		}
	}

	private function selectquestions()
	{
		$sessionvars = $this->exam->getExamSessionBySessionid();
		if($this->data['currentbasic']['basicexam']['examnumber'])
		{
			$overflow = false;
			$ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
			if(!$ids)$ids = '0';
			$number = array();
			if($ids)
			{
				$ids = explode(',',$ids);
				foreach($ids as $t)
				{
					$num = $this->favor->getExamUseNumber($this->_user['sessionuserid'],$t,$this->data['currentbasic']['basicid']);
					$number['child'][$t] = $num;
					$number['all'] = intval($number['all'])+$num;
				}
			}
			if($this->data['currentbasic']['basicexam']['selectrule'])
			{
				if($number['all'] >= $this->data['currentbasic']['basicexam']['examnumber'])
				{
					$overflow = true;
				}
			}
			else
			{
				if($number['child'][$sessionvars['examsessionkey']] >= $this->data['currentbasic']['basicexam']['examnumber'])
				{
					$overflow = true;
				}
			}
			if($overflow)
			{
				$message = array(
					'statusCode' => 300,
					"message" => "您的考试次数已经用完了！"
				);
				$this->G->R($message);
			}
		}
		if($this->data['currentbasic']['basicexam']['selectrule'])
		{
			$ids = explode(',',trim($this->data['currentbasic']['basicexam']['self'],', '));
			$p = rand(0,count($ids)-1);
			$examid = $ids[$p];
		}
		else
		$examid = $this->ev->get('examid');
		$r = $this->exam->getExamSettingById($examid);
		if(!$r['examid'])
		{
			$message = array(
				'statusCode' => 300,
				"message" => "参数错误，尝试退出后重新进入"
			);
			$this->G->R($message);
		}
		else
		{
			if($r['examtype'] == 1)
			{
				$questionids = $this->question->selectQuestions($examid,$this->data['currentbasic']);
				$questions = array();
				$questionrows = array();
				foreach($questionids['question'] as $key => $p)
				{
					$ids = "";
					if(count($p))
					{
						foreach($p as $t)
						{
							$ids .= $t.',';
						}
						$ids = trim($ids," ,");
						if(!$ids)$ids = 0;
						$questions[$key] = $this->exam->getQuestionListByIds($ids);
					}
				}
				foreach($questionids['questionrow'] as $key => $p)
				{
					$ids = "";
					if(is_array($p))
					{
						if(count($p))
						{
							foreach($p as $t)
							{
								$questionrows[$key][$t] = $this->exam->getQuestionRowsById($t);
							}
						}
					}
					else $questionrows[$key][$p] = $this->exam->getQuestionRowsById($p);
				}
				$sargs['examsessionquestion'] = array('questionids'=>$questionids,'questions'=>$questions,'questionrows'=>$questionrows);
				$sargs['examsessionsetting'] = $questionids['setting'];
				$sargs['examsessionstarttime'] = TIME;
				$sargs['examsession'] = $questionids['setting']['exam'];
				$sargs['examsessiontime'] = $questionids['setting']['examsetting']['examtime']>0?$questionids['setting']['examsetting']['examtime']:60;
				$sargs['examsessionstatus'] = 0;
				$sargs['examsessiontype'] = 2;
				$sargs['examsessionsign'] = '';
				$sargs['examsessionuseranswer'] = '';
				$sargs['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$sargs['examsessionkey'] = $examid;
				$sargs['examsessionissave'] = 0;
				$sargs['examsessionsign'] = '';
				$sargs['examsessionuserid'] = $this->_user['sessionuserid'];
				if($sessionvars['examsessionid'])
				$this->exam->modifyExamSession($sargs);
				else
				$this->exam->insertExamSession($sargs);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题完毕，转入试卷页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-paper"
				);
				$this->G->R($message);
			}
			elseif($r['examtype'] == 2)
			{
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$questions = array();
				$questionrows = array();
				foreach($r['examquestions'] as $key => $p)
				{
					$qids = '';
					$qrids = '';
					if($p['questions'])$qids = trim($p['questions']," ,");
					if($qids)
					$questions[$key] = $this->exam->getQuestionListByIds($qids);
					if($p['rowsquestions'])$qrids = trim($p['rowsquestions']," ,");
					if($qrids)
					{
						$qrids = explode(",",$qrids);
						foreach($qrids as $t)
						{
							$qr = $this->exam->getQuestionRowsById($t);
							if($qr)
							$questionrows[$key][$t] = $qr;
						}
					}
				}
				$args['examsessionquestion'] = array('questions'=>$questions,'questionrows'=>$questionrows);
				$args['examsessionsetting'] = $r;
				$args['examsessionstarttime'] = TIME;
				$args['examsession'] = $r['exam'];
				$args['examsessionscore'] = 0;
				$args['examsessionuseranswer'] = '';
				$args['examsessionscorelist'] = '';
				$args['examsessionsign'] = '';
				$args['examsessiontime'] = $r['examsetting']['examtime'];
				$args['examsessionstatus'] = 0;
				$args['examsessiontype'] = 2;
				$args['examsessionkey'] = $r['examid'];
				$args['examsessionissave'] = 0;
				$args['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$args['examsessionuserid'] = $this->_user['sessionuserid'];
				if($sessionvars['examsessionid'])
				$this->exam->modifyExamSession($args);
				else
				$this->exam->insertExamSession($args);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题完毕，转入试卷页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-paper"
				);
				$this->G->R($message);
			}
			else
			{
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$args['examsessionquestion'] = $r['examquestions'];
				$args['examsessionsetting'] = $r;
				$args['examsessionstarttime'] = TIME;
				$args['examsession'] = $r['exam'];
				$args['examsessionscore'] = 0;
				$args['examsessionuseranswer'] = '';
				$args['examsessionscorelist'] = '';
				$args['examsessionsign'] = '';
				$args['examsessiontime'] = $r['examsetting']['examtime'];
				$args['examsessionstatus'] = 0;
				$args['examsessiontype'] = 2;
				$args['examsessionkey'] = $r['examid'];
				$args['examsessionissave'] = 0;
				$args['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$args['examsessionuserid'] = $this->_user['sessionuserid'];
				if($sessionvars['examsessionid'])
				$this->exam->modifyExamSession($args);
				else
				$this->exam->insertExamSession($args);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题完毕，转入试卷页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exam-paper"
				);
				$this->G->R($message);
			}
		}
	}

	private function index()
	{
		$page = $this->ev->get('page');
		$ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
		if(!$ids)$ids = '0';
		$exams = $this->exam->getExamSettingList($page,20,array(array("AND","find_in_set(examid,:examid)",'examid',$ids)));
		$number = array();
		if($ids)
		{
			$ids = explode(',',$ids);
			foreach($ids as $t)
			{
				$num = $this->favor->getExamUseNumber($this->_user['sessionuserid'],$t,$this->data['currentbasic']['basicid']);
				$number['child'][$t] = $num;
				$number['all'] = intval($number['all'])+$num;
			}
		}
		$sessionvars = $this->exam->getExamSessionByUserid($this->_user['sessionuserid'],$this->data['currentbasic']['basicid']);
		if($sessionvars && ($sessionvars['examsessionbasic'] == $this->_user['sessioncurrent']) && ($sessionvars['examsessionstatus'] < 2) && ($sessionvars['examsessiontype'] == 2))
		$this->tpl->assign('sessionvars',$sessionvars);
		$this->tpl->assign('number',$number);
		$this->tpl->assign('exams',$exams);
		$this->tpl->display('exam');
	}
}


?>
