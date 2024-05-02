<?php

use function Livewire\Volt\{state};

//

?>

<x-layouts.app>
    @volt
        <div x-data="{
            turn: 'X',
            firstTurn: 'X',
            cells: Array(9).fill(''),
            isAnimating: Array(9).fill(false),
            isBot: true,
            difficulties: ['Easy', 'Medium', 'Hard', 'PvP'],
            selectedDifficulty: 'Easy',
            winner: null,
            isDraw: false,
            score: {
                X: 0,
                O: 0,
                draw: 0,
            },
            combos: [
                [0, 1, 2],
                [3, 4, 5],
                [6, 7, 8],
                [0, 3, 6],
                [1, 4, 7],
                [2, 5, 8],
                [0, 4, 8],
                [2, 4, 6],
            ],
            moves: [],
            resetGame() {
                this.cells = Array(9).fill('');
                this.winner = null;
                this.isDraw = false;
                if (this.isBot && this.firstTurn === 'O') {
                    this.startBot(true);
                }
            },
            resetScore() {
                this.score = {
                    X: 0,
                    O: 0,
                    draw: 0,
                }
            },
            resetTurn() {
                this.firstTurn = 'X';
                this.turn = 'X';
            },
            startBot(firstTurnBot) {
                if (this.isBot && !this.winner && !this.isDraw) {
                    this.botMove(firstTurnBot);
                }
            },
            setBot(value) {
                this.isBot = !(value === 'PvP');
                this.resetScore();
                this.resetTurn();
                this.resetGame();
            },
            humanMove(index) {
                if (!this.cells.includes('') || this.winner) {
                    this.resetGame();
                    return;
                }
        
                if (this.cells[index] !== '' || this.winner) {
                    return;
                }
        
                this.move(index);
                this.startBot(false);
            },
            botMove(firstTurnBot) {
                if (this.selectedDifficulty === 'Medium' || this.selectedDifficulty === 'Hard') {
                    if (firstTurnBot) {
                        this.delayBotMove(4);
                        return;
                    }
        
                    let botIndex = this.findBestMove();
                    this.delayBotMove(botIndex);
                } else { // easy
                    let emptyCells = this.getEmptyCells();
                    let botIndex = emptyCells[Math.floor(Math.random() * emptyCells.length)];
                    this.delayBotMove(botIndex);
                }
        
                this.moves = [];
            },
            delayBotMove(index) {
                setTimeout(() => {
                    this.move(index);
                }, 300);
            },
            findBestMove() {
                let minimax = this.minimax(this.cells, 0, true);
                let bestScore = -Infinity
                let depthZero = this.moves.filter((el) => {
                    if (el.depth == 0) bestScore = Math.max(el.score, bestScore)
                    return el.depth === 0
                });
                let bestMove = depthZero.filter((el) => el.score >= bestScore);
                let bestMoveRandom = bestMove[Math.floor(Math.random() * bestMove.length)];
        
                return bestMoveRandom.move;
            },
            minimax(board, depth, isMaximizingPlayer) {
                let emptyCells = this.getEmptyCells();
                let maxDepth = this.selectedDifficulty === 'Hard' ? emptyCells.length : 2;
                {{-- let maxDepth = 5; --}}
                let winner = this.checkWinningPlayer();
                if (winner) {
                    return winner === 'O' ? 10 : (winner === 'X' ? -10 : 0);
                } else if (board.every((cell) => cell !== '')) {
                    return 0;
                }
        
                if (depth == maxDepth) {
                    return 0;
                }
        
                if (isMaximizingPlayer) {
                    let bestScore = -Infinity;
                    for (let index of emptyCells) {
                        board[index] = 'O';
                        let score = this.minimax(board, depth + 1, false);
                        board[index] = '';
                        bestScore = Math.max(bestScore, score);
                        this.moves.push({ depth, score, move: index, turn: 'O' });
                    }
                    return bestScore;
                } else {
                    let bestScore = Infinity;
                    for (let index of emptyCells) {
                        board[index] = 'X';
                        let score = this.minimax(board, depth + 1, true);
                        board[index] = '';
                        bestScore = Math.min(bestScore, score);
                        this.moves.push({ depth, score, move: index, turn: 'X' });
                    }
                    return bestScore;
                }
        
            },
            checkWinningPlayer() {
                for (let combo of this.combos) {
                    const [a, b, c] = combo;
                    if (this.cells[a] !== '' &&
                        this.cells[a] === this.cells[b] &&
                        this.cells[b] === this.cells[c]
                    ) {
                        return this.cells[a];
                    }
                }
        
                return null;
            },
            getEmptyCells() {
                return this.cells.map((cell, index) => cell === '' ? index : -1).filter(index => index !== -1);
            },
            move(index) {
                this.animateCell(index);
                this.setCell(index);
                this.checkWinner();
                this.toggleTurn();
                this.toggleFirstTurn();
            },
            toggleTurn() {
                this.turn = this.turn === 'X' ? 'O' : 'X';
            },
            toggleFirstTurn() {
                if (this.winner || this.isDraw) {
                    this.firstTurn = this.firstTurn === 'X' ? 'O' : 'X';
                    this.turn = this.firstTurn;
                }
            },
            checkWinner() {
                let winner = this.checkWinningPlayer();
                if (winner) {
                    this.winner = winner;
                    this.score[this.winner]++;
                }
        
                if (!winner && !this.cells.includes('')) {
                    this.isDraw = true;
                    this.score['draw']++;
                }
            },
            setCell(index) {
                this.cells[index] = this.turn;
            },
            animateCell(index) {
                this.isAnimating[index] = true;
                setTimeout(() => this.isAnimating[index] = false, 200);
            },
        }">
            <x-header title="Tic Tac Toe" size="text-3xl text-primary">
                <x-slot:actions>
                    <x-theme-toggle class="btn" title="Toggle Theme" />
                    <x-button label="" class="" x-on:click="$wire.drawerSettings = true" responsive
                        icon="o-adjustments-horizontal" title="Settings" />
                </x-slot:actions>
            </x-header>

            <div class="m-auto w-full h-[calc(100vh-8rem)] justify-center items-center flex flex-col gap-8 px-4">
                {{-- <div class="text-2xl">Winner: <span x-text="winner"></span></div> --}}
                <select class="max-w-xs max-24 select select-bordered select-sm" x-model="selectedDifficulty"
                    x-on:input="setBot($event.target.value)">
                    <template x-for="difficulty in difficulties">
                        <option x-text="difficulty" x-bind:selected="difficulty === selectedDifficulty"></option>
                    </template>
                </select>
                <div class="grid grid-cols-3 grid-rows-3">
                    <template x-for="(cell, index) in cells">
                        <div class="flex items-center justify-center border-2 w-28 h-28 lg:w-32 lg:h-32"
                            x-on:click="humanMove(index)"
                            x-bind:class="{
                                'border-t-0': [0, 1, 2].includes(index),
                                'border-r-0': [2, 5, 8].includes(index),
                                'border-b-0': [6, 7, 8].includes(index),
                                'border-l-0': [0, 3, 6].includes(index),
                            }">
                            <div class="text-8xl" x-bind:class="{ 'animate-popout': isAnimating[index] }" x-text="cell">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid w-full grid-cols-3 gap-4">
                    <div class="py-2 text-center transition-all border-2 rounded"
                        x-bind:class="{
                            'bg-info border-info': !winner && !isDraw && turn === 'X',
                            'bg-success border-success': winner === 'X',
                        }">
                        <div>
                            Player (X)
                        </div>
                        <div class="text-2xl font-bold" x-text="score.X"></div>
                    </div>
                    <div class="py-2 text-center transition-all border-2 rounded"
                        x-bind:class="{
                            'bg-warning border-warning': isDraw,
                        }">
                        <div>
                            Draw
                        </div>
                        <div class="text-2xl font-bold" x-text="score.draw"></div>
                    </div>
                    <div class="py-2 text-center transition-all border-2 rounded"
                        x-bind:class="{
                            'bg-info border-info': !winner && !isDraw && turn === 'O',
                            'bg-success border-success': winner === 'O',
                        }">
                        <div>
                            <template x-if="isBot">
                                <span>Bot</span>
                            </template>
                            <template x-if="!isBot">
                                <span>Player</span>
                            </template>
                            (O)
                        </div>
                        <div class="text-2xl font-bold" x-text="score.O"></div>
                    </div>
                </div>

                {{-- <div>
                    <x-button icon="o-arrow-path" label="Restart" x-on:click="resetGame" />
                </div> --}}
            </div>
        </div>
    @endvolt
</x-layouts.app>
