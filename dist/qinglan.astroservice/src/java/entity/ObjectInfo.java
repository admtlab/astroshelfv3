/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonBackReference;
import org.codehaus.jackson.annotate.JsonIgnore;
import org.codehaus.jackson.annotate.JsonManagedReference;

/**
 *
 * @author roxy
 */
@Entity
@Table(name = "object_info")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "ObjectInfo.findAll", query = "SELECT o FROM ObjectInfo o"),
    @NamedQuery(name = "ObjectInfo.findByObjectId", query = "SELECT o FROM ObjectInfo o WHERE o.objectId = :objectId"),
    @NamedQuery(name = "ObjectInfo.findByName", query = "SELECT o FROM ObjectInfo o WHERE o.name = :name"),
    @NamedQuery(name = "ObjectInfo.findByRa", query = "SELECT o FROM ObjectInfo o WHERE o.ra = :ra"),
    @NamedQuery(name = "ObjectInfo.findByDec", query = "SELECT o FROM ObjectInfo o WHERE o.dec = :dec"),
    @NamedQuery(name = "ObjectInfo.findByRaType", query = "SELECT o FROM ObjectInfo o WHERE o.raType = :raType"),
    @NamedQuery(name = "ObjectInfo.findByZ", query = "SELECT o FROM ObjectInfo o WHERE o.z = :z"),
    @NamedQuery(name = "ObjectInfo.findByColor", query = "SELECT o FROM ObjectInfo o WHERE o.color = :color"),
    @NamedQuery(name = "ObjectInfo.findByMagnitude", query = "SELECT o FROM ObjectInfo o WHERE o.magnitude = :magnitude"),
    @NamedQuery(name = "ObjectInfo.findByObjType", query = "SELECT o FROM ObjectInfo o WHERE o.objType = :objType")})
public class ObjectInfo implements Serializable {
    @ManyToMany(cascade = CascadeType.PERSIST, mappedBy = "objectInfoCollection")
    private Collection<Annotation> annotationCollection;
    // @Max(value=?)  @Min(value=?)//if you know range of your decimal fields consider using these annotations to enforce field validation
    @Column(name = "Z")
    private Double z;
    //@Basic(optional = false)
    //@NotNull
    @Column(name = "specClass")
    private short specClass;
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "object_id")
    private Long objectId;
    @Basic(optional = false)
    @NotNull
    @Lob
    @Size(min = 1, max = 65535)
    @Column(name = "survey_obj_id")
    private String surveyObjId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 45)
    @Column(name = "name")
    private String name;
    @Basic(optional = false)
    @NotNull
    @Column(name = "_RA_")
    private float ra;
    @Basic(optional = false)
    @NotNull
    @Column(name = "_DEC_")
    private float dec;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 11)
    @Column(name = "RA_TYPE")
    private String raType;
    // @Max(value=?)  @Min(value=?)//if you know range of your decimal fields consider using these annotations to enforce field validation
    @Column(name = "color")
    private Double color;
    @Column(name = "magnitude")
    private Double magnitude;
    @Size(max = 10)
    @Column(name = "obj_type")
    private String objType;
    @JoinColumn(name = "survey_id", referencedColumnName = "survey_id")
    @ManyToOne(optional = false)
    private Survey surveyId;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "objSrcId")
    private Collection<ObjMapping> objMappingSrcCollection;
    @OneToMany(cascade = CascadeType.ALL, mappedBy = "objTarId")
    private Collection<ObjMapping> objMappingTarCollection;
//    @OneToMany(cascade = CascadeType.ALL, mappedBy = "objTarId")
//    private Collection<AnnoToObj> annoToObjCollection;

    public ObjectInfo() {
    }

    public ObjectInfo(Long objectId) {
        this.objectId = objectId;
    }

    public ObjectInfo(Long objectId, String surveyObjId, String name, float ra, float dec, String raType) {
        this.objectId = objectId;
        this.surveyObjId = surveyObjId;
        this.name = name;
        this.ra = ra;
        this.dec = dec;
        this.raType = raType;
    }

    public Long getObjectId() {
        return objectId;
    }

    public void setObjectId(Long objectId) {
        this.objectId = objectId;
    }

    public String getSurveyObjId() {
        return surveyObjId;
    }

    public void setSurveyObjId(String surveyObjId) {
        this.surveyObjId = surveyObjId;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public float getRa() {
        return ra;
    }

    public void setRa(float ra) {
        this.ra = ra;
    }

    public float getDec() {
        return dec;
    }

    public void setDec(float dec) {
        this.dec = dec;
    }

    public String getRaType() {
        return raType;
    }

    public void setRaType(String raType) {
        this.raType = raType;
    }

    public Double getColor() {
        return color;
    }

    public void setColor(Double color) {
        this.color = color;
    }

    public Double getMagnitude() {
        return magnitude;
    }

    public void setMagnitude(Double magnitude) {
        this.magnitude = magnitude;
    }

    public String getObjType() {
        return objType;
    }

    public void setObjType(String objType) {
        this.objType = objType;
    }

//    @XmlTransient
//    @JsonIgnore
    public Survey getSurveyId() {
        return surveyId;
    }

    public void setSurveyId(Survey surveyId) {
        this.surveyId = surveyId;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<ObjMapping> getObjMappingSrcCollection() {
        return objMappingSrcCollection;
    }

    public void setObjMappingCollection(Collection<ObjMapping> objMappingSrcCollection) {
        this.objMappingSrcCollection = objMappingSrcCollection;
    }

    @XmlTransient
    @JsonIgnore
    public Collection<ObjMapping> getObjMappingTarCollection() {
        return objMappingTarCollection;
    }

    public void setObjMappingCollection1(Collection<ObjMapping> objMappingTarCollection) {
        this.objMappingTarCollection = objMappingTarCollection;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (objectId != null ? objectId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof ObjectInfo)) {
            return false;
        }
        ObjectInfo other = (ObjectInfo) object;
        if ((this.objectId == null && other.objectId != null) || (this.objectId != null && !this.objectId.equals(other.objectId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.ObjectInfo[ objectId=" + objectId + " ]";
    }

    public Double getZ() {
        return z;
    }

    public void setZ(Double z) {
        this.z = z;
    }

    public short getSpecClass() {
        return specClass;
    }

    public void setSpecClass(short specClass) {
        this.specClass = specClass;
    }

    @XmlTransient
    @JsonIgnore
    //@JsonBackReference("annotation-object")
    public Collection<Annotation> getAnnotationCollection() {
        return annotationCollection;
    }

    public void setAnnotationCollection(Collection<Annotation> annotationCollection) {
        this.annotationCollection = annotationCollection;
    }
    
}
