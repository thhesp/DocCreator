package de.ts.doccreator.model;

public class DocVar {
	
	private String type;
	private String name;
	
	public DocVar(String name, String type){
		this.name = name;
		this.type = type;
	}
	
	public String getType(){
		return type;
	}
	
	public String getName(){
		return name;
	}

}
