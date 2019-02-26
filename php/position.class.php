<?php

	/*Essa class será utilizada para dar o formato triângular ao grafo exibido na tela*/
	
	class Position {
		private $x;
		private $y;

		//Margem em que os vértices se distanciarão entre os mesmos
		private $margin;

		//centro onde será exibido o vértice inicial
		private $center;

		function __construct(){
			$x = 0;
			$y = 0;
			$center = 0;
			$margin = 0;
		}

		public function marginIncrement(){
			$this->margin = $this->margin + 1000;
		}

		public function setCenter($center){
			$this->center = $center;
		}

		public function setMargin($margin){
			$this->margin = $margin;
		}

		public function setX($x){
			$this->x = $x;
		}


		public function setY($y){
			$this->y = $y;
		}

		/*************************************************
		Essas funções servem para rotacionar os vértices
		em torno do vértice central

							|
							|
							|
					4º		|		1º
							|
			________________|________________
							|
							|
					3º		|		2º
							|
							|
							|
		*************************************************/

		/*
		Verifica em qual quadrante se encontra o vértice para então
		encontrar a próxima localização de x

		* Verifica os valores de x e y
			-> se ambos forem iguais ao $center então esse será o segundo ponto a 
			ser inserido no grafo, então x = 0
			-> se x for menor que o $center e y maior, o vértice se encontra 
			no quarto quadrante, então x recebe o valor da margem para retornar
			ao primeiro quadrante
			-> se x for maior ou igual ao $center e y também, o vértice se encontra
			no primeiro quadrante, então x deve receber o valor da margem
			-> se x for maior que o $center e y menor, o vértice está no segundo
			quadrante, então em x será subtraido com o valor da margem para que o 
			ponto passe	ao terceiro quadrante
			-> se x e y forem menores que o $center então os dois se encontram no 
			quarto quadrante, portanto será subtraído de x o valor da margem

		*/

		public function getX(){

			if($this->x == $this->center && $this->y == $this->center){
				return $this->center;
			}
			else if($this->x < $this->center && $this->y >= $this->center){
				$this->x += $this->margin; 
				return $this->x; 
			}
			else if($this->x >= $this->center && $this->y >= $this->center){
				$this->x += $this->margin; 
				return $this->x; 
			}
			else if($this->x >= $this->center && $this->y <= $this->center){
				$this->x -= $this->margin; 
				return $this->x;;
			}
			else if($this->x < $this->center && $this->y <= $this->center){
				$this->x -= $this->margin; 
				return $this->x;
			}

		}

		/*
		Verifica em qual quadrante se encontra o vértice para então
		encontrar a próxima localização de y
		
		- Verifica os valores de x e y
			-> se ambos forem iguais ao $center então esse será o segundo ponto a 
			ser inserido no grafo, então y recebe o valor da margem
			-> se y for maior que o $center e x menor, o vértice se encontra no 
			quarto quadrante, então y é incrementado com o valor da margem para que
			o ponto passe para o primeiro quadrante
			-> se x for maior ou igual ao $center e y também, o vértice se encontra
			no primeiro quadrante, então y é decrementado com o valor da margem
			-> se x for maior que o $center e y menor, o vértice está no segundo
			quadrante, então em y será subtraido com o valor da margem para que o 
			ponto passe	ao terceiro quadrante
			-> se x e y forem menores que o $center então os dois se encontram no 
			quarto quadrante, portanto será acrescentado a y o valor da margem

		*/

		public function getY(){

			if($this->x == $this->center && $this->y == $this->center){
				$this->y += $this->margin;
				return $this->y;
			}
			else if($this->y >= $this->center && $this->x <= $this->center){
				$this->y += $this->margin;
				return $this->y;
			}
			else if($this->y > $this->center && $this->x >= $this->center){
				$this->y -= $this->margin;
				return $this->y;
			}
			else if($this->y <= $this->center && $this->x >= $this->center){
				$this->y -= $this->margin;
				return $this->y;
			}
			else if($this->y < $this->center && $this->x <= $this->center){
				$this->y += $this->margin;
				return $this->y;
			}

		}

		/*
		Verifica se o quadrante foi alterado
		-Toda vez que x OU y forem iguais a 0 significa que
		o próximo ponto se encontrará no próximo quadrante
		*/

		function getQuadrant(){
			if($this->x == 0){
				return 1;
			}
			if($this->y == 0){
				return 1;
			}

			return 0;
		}

		function getCenter(){
			return $this->center;
		}
	}
?>