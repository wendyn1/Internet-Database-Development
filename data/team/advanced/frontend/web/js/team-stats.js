/**
 * 项目：2026世界杯球队信息系统
 * 文件职责：对比页胜平负饼图与JSON局部刷新（C6）
 * 作者：邓佳怡（学号 2412612）
 * 创建日期：2026-11-25
 * 主要修改者：邓佳怡（学号 2412612）
 * 说明：不依赖第三方绘图库，使用原生 Canvas 绘制，离线也可运行。
 */
(function () {
    'use strict';

    var COLORS = {
        wins: '#2e7d32',
        draws: '#f9a825',
        losses: '#c62828'
    };

    function toInt(value) {
        var num = parseInt(value, 10);
        return isNaN(num) ? 0 : num;
    }

    function drawPie(canvas, data) {
        var size = 180;
        var ratio = window.devicePixelRatio || 1;
        canvas.width = size * ratio;
        canvas.height = size * ratio;
        canvas.style.width = size + 'px';
        canvas.style.height = size + 'px';

        var ctx = canvas.getContext('2d');
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, size, size);

        var values = [toInt(data.wins), toInt(data.draws), toInt(data.losses)];
        var total = values[0] + values[1] + values[2];
        if (total <= 0) {
            return false;
        }

        var start = -Math.PI / 2;
        var keys = ['wins', 'draws', 'losses'];
        var cx = size / 2;
        var cy = size / 2;
        var radius = size / 2 - 4;

        for (var i = 0; i < values.length; i++) {
            if (values[i] === 0) {
                continue;
            }
            var angle = Math.PI * 2 * values[i] / total;
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.arc(cx, cy, radius, start, start + angle);
            ctx.closePath();
            ctx.fillStyle = COLORS[keys[i]];
            ctx.fill();
            start += angle;
        }
        return true;
    }

    function setText(card, selector, value) {
        var node = card.querySelector(selector);
        if (node) {
            node.textContent = value;
        }
    }

    function renderCard(card, data) {
        setText(card, '.stat-played', toInt(data.played));
        setText(card, '.stat-wins', toInt(data.wins));
        setText(card, '.stat-draws', toInt(data.draws));
        setText(card, '.stat-losses', toInt(data.losses));
        setText(card, '.stat-goals-for', toInt(data.goals_for));
        setText(card, '.stat-goals-against', toInt(data.goals_against));

        var canvas = card.querySelector('canvas.team-pie');
        var empty = card.querySelector('.team-pie-empty');
        if (!canvas || !empty) {
            return;
        }
        if (toInt(data.played) <= 0) {
            canvas.style.display = 'none';
            empty.style.display = 'block';
            empty.textContent = '暂无已结束比赛，不绘制全零饼图。';
            return;
        }
        var drawn = drawPie(canvas, data);
        canvas.style.display = drawn ? 'block' : 'none';
        empty.style.display = drawn ? 'none' : 'block';
        if (!drawn) {
            empty.textContent = '暂无已结束比赛，不绘制全零饼图。';
        }
    }

    function showError(card, message) {
        var errorBox = card.querySelector('.team-pie-error');
        if (errorBox) {
            errorBox.style.display = 'block';
            var text = errorBox.querySelector('.team-pie-error-text');
            if (text) {
                text.textContent = message;
            }
        }
    }

    function clearError(card) {
        var errorBox = card.querySelector('.team-pie-error');
        if (errorBox) {
            errorBox.style.display = 'none';
        }
    }

    function initRoot(root) {
        var url = root.getAttribute('data-stats-url');
        if (!url) {
            return;
        }
        var controllers = {};
        var tokens = {};

        function load(card, teamId) {
            var side = card.getAttribute('data-side') || '';
            if (!teamId) {
                return;
            }
            if (controllers[side]) {
                controllers[side].abort();
            }
            var controller = window.AbortController ? new AbortController() : null;
            controllers[side] = controller;
            var token = (tokens[side] || 0) + 1;
            tokens[side] = token;

            clearError(card);
            card.classList.add('is-loading');

            var options = {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                credentials: 'same-origin'
            };
            if (controller) {
                options.signal = controller.signal;
            }

            fetch(url + (url.indexOf('?') === -1 ? '?' : '&') + 'team_id=' + encodeURIComponent(teamId), options)
                .then(function (response) {
                    return response.json().catch(function () {
                        return {success: false, message: '返回数据无法解析，请重试。'};
                    });
                })
                .then(function (body) {
                    if (tokens[side] !== token) {
                        return;
                    }
                    if (!body || body.success !== true || !body.data) {
                        throw new Error((body && body.message) || '统计加载失败，请重试。');
                    }
                    renderCard(card, body.data);
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                    if (tokens[side] !== token) {
                        return;
                    }
                    showError(card, error.message || '统计加载失败，请重试。');
                })
                .then(function () {
                    if (tokens[side] === token) {
                        card.classList.remove('is-loading');
                    }
                });
        }

        var cards = root.querySelectorAll('.compare-card[data-side]');
        for (var i = 0; i < cards.length; i++) {
            (function (card) {
                var canvas = card.querySelector('canvas.team-pie');
                var played = card.getAttribute('data-played');
                if (canvas && played !== null) {
                    renderCard(card, {
                        played: played,
                        wins: card.getAttribute('data-wins'),
                        draws: card.getAttribute('data-draws'),
                        losses: card.getAttribute('data-losses'),
                        goals_for: card.getAttribute('data-goals-for'),
                        goals_against: card.getAttribute('data-goals-against')
                    });
                }
                var retry = card.querySelector('.team-pie-retry');
                if (retry) {
                    retry.addEventListener('click', function () {
                        var select = root.querySelector('[name="' + (card.getAttribute('data-side') === 'a' ? 'team_a_id' : 'team_b_id') + '"]');
                        if (select) {
                            load(card, select.value);
                        }
                    });
                }
            })(cards[i]);
        }

        var selects = root.querySelectorAll('select[data-compare-select]');
        for (var j = 0; j < selects.length; j++) {
            (function (select) {
                select.addEventListener('change', function () {
                    var side = select.getAttribute('data-side');
                    var card = root.querySelector('.compare-card[data-side="' + side + '"]');
                    if (!card) {
                        return;
                    }
                    var selectedOption = select.options[select.selectedIndex];
                    var nameNode = card.querySelector('.compare-team-name');
                    if (nameNode && selectedOption && selectedOption.value) {
                        var name = selectedOption.text.replace(/（ID\s*\d+）$/, '');
                        var nameLink = nameNode.querySelector('a');
                        if (nameLink) {
                            nameLink.textContent = name;
                        } else {
                            nameNode.textContent = name;
                        }
                    }
                    load(card, select.value);
                });
            })(selects[j]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('team-compare');
        if (root) {
            initRoot(root);
        }
    });
})();
