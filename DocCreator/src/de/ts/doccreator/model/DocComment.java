package de.ts.doccreator.model;

import de.ts.doccreator.model.DocUtil.TYPE;

public abstract class DocComment {
	
	protected String name;
	protected TYPE type;
	protected String description;
	
	public void setName(String name){
		this.name = name;
	}
	
	public void setType(String type){
		type = type.toUpperCase();
		try{
			this.type = TYPE.valueOf(type);
		}catch(IllegalArgumentException e){
			this.type = TYPE.UNKNOWN;
		}
	}
	
	public void setDescription(String description){
		this.description = description;
	}
	
	public String getName(){
		return name;
	}
	
	public String getType(){
		return type.toString();
	}
	
	public String getDescription(){
		return description;
	}
	
	public String toString(){
		
		String output = "\nName: " +name + "\n" +
						" Type " + type + "\n" +
						" Description " + description +"\n";
		
		return output;
	}
}
